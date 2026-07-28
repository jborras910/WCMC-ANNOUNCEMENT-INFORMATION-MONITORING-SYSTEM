#!/usr/bin/env bash
#
# One-time production rollout for the RBAC + Departments release.
# Run this FROM THE PROJECT ROOT on the prod server (Git Bash), alone,
# with no one else around to ask — every step prints what it's doing and
# what to do if it fails. Read a step's comment before you hit "y".
#
# Why this isn't just "git pull && php artisan migrate && php artisan db:seed":
# one of the migrations DROPS the legacy `department`/`role` string columns.
# If it runs before the seeders read those columns, real prod data (which
# departments/roles your actual users belong to) is gone before it's ever
# copied into the new tables. This script forces the correct order and
# pauses for you to eyeball the data before that drop happens.
#
# Safe to re-run: every step here is idempotent (firstOrCreate / migrate
# only applies pending migrations), so if you stop partway through and
# re-run, it picks up where it left off.
#
# IF SOMETHING GOES WRONG: this script stops immediately (it does not limp
# forward) and prints the exact commands to undo whatever happened. Scroll
# up to find the first line starting with "STOPPED —" and follow it.

set -euo pipefail
cd "$(dirname "$0")/.."

BACKUP_FILE=""
PREV_COMMIT=""

on_error() {
    echo
    echo "STOPPED — something failed at line $1. Nothing after that line ran."
    echo
    if [ -n "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
        echo "Your pre-deploy backup is safe at: $BACKUP_FILE"
        echo "To restore the database to how it was before this script ran:"
        echo "  mysql -h \"$DB_HOST\" -u \"$DB_USERNAME\" ${DB_PASSWORD:+-p'<your DB password>'} \"$DB_DATABASE\" < $BACKUP_FILE"
    else
        echo "No backup was taken yet — the database has not been touched."
    fi
    if [ -n "$PREV_COMMIT" ]; then
        echo
        echo "To undo the code changes and go back to what was running before:"
        echo "  git reset --hard $PREV_COMMIT"
        echo "  composer install --no-dev --optimize-autoloader"
    fi
    echo
    echo "Once restored, fix whatever caused the failure above and re-run this script from the top."
    exit 1
}
trap 'on_error $LINENO' ERR

echo "== WCMC AIMS — RBAC + Departments deploy =="
echo "Working directory: $(pwd)"
echo

# ---------------------------------------------------------------------------
# 0. Pre-flight — fail fast if something's missing, before touching anything
# ---------------------------------------------------------------------------
echo "== Checking required tools =="
missing=0
for cmd in php composer git mysqldump mysql; do
    if command -v "$cmd" >/dev/null 2>&1; then
        echo "  [ok] $cmd"
    else
        echo "  [MISSING] $cmd — install it or add it to PATH before continuing."
        missing=1
    fi
done
[ "$missing" -eq 0 ] || { echo; echo "Stopping — install the missing tool(s) above first."; exit 1; }

if [ ! -f .env ]; then
    echo "No .env found here — run this from the Laravel project root (where artisan lives)."
    exit 1
fi
echo "  [ok] .env found"

if [ -n "$(git status --porcelain)" ]; then
    echo
    echo "You have uncommitted local changes on this server:"
    git status --short
    echo "Commit or stash them first — this script runs 'git pull', which can fail or"
    echo "silently merge on top of unexpected local edits otherwise."
    exit 1
fi
echo "  [ok] working tree is clean"
echo

PREV_COMMIT=$(git rev-parse HEAD)
echo "Current commit (in case of rollback): $PREV_COMMIT"
echo

read -p "This will modify the production database. Continue? [y/N] " confirm
[[ "$confirm" == "y" || "$confirm" == "Y" ]] || { echo "Aborted. Nothing was changed."; exit 1; }

# ---------------------------------------------------------------------------
# 1. Backup the database first. Non-negotiable.
# ---------------------------------------------------------------------------
DB_DATABASE=$(grep -E '^DB_DATABASE=' .env | cut -d '=' -f2-)
DB_USERNAME=$(grep -E '^DB_USERNAME=' .env | cut -d '=' -f2-)
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' .env | cut -d '=' -f2-)
DB_HOST=$(grep -E '^DB_HOST=' .env | cut -d '=' -f2-)

BACKUP_FILE="backup_pre_rbac_deploy_$(date +%Y%m%d_%H%M%S).sql"
echo
echo "Backing up '$DB_DATABASE' to $BACKUP_FILE ..."
mysqldump -h "$DB_HOST" -u "$DB_USERNAME" ${DB_PASSWORD:+-p"$DB_PASSWORD"} "$DB_DATABASE" > "$BACKUP_FILE"

if [ ! -s "$BACKUP_FILE" ]; then
    echo "Backup file is empty — something went wrong. Stopping before touching anything."
    exit 1
fi
echo "Backup OK ($(du -h "$BACKUP_FILE" | cut -f1)). Keep this file until you've verified the deploy works."
echo

# ---------------------------------------------------------------------------
# 2. Pull code + install dependencies (spatie/laravel-permission is new)
# ---------------------------------------------------------------------------
echo "== Pulling latest code =="
git pull

echo "== Installing dependencies =="
composer install --no-dev --optimize-autoloader

# ---------------------------------------------------------------------------
# 3. Migrate up to (but not including) the legacy-column drop
# ---------------------------------------------------------------------------
echo
echo "== Creating new tables (permissions, roles, departments, department_id columns) =="
php artisan migrate --path=database/migrations/2026_07_27_163952_create_permission_tables.php --force
php artisan migrate --path=database/migrations/2026_07_28_010000_create_departments_table.php --force
php artisan migrate --path=database/migrations/2026_07_28_010001_add_department_id_to_users_and_slides.php --force

# ---------------------------------------------------------------------------
# 4. Seed roles/permissions, then backfill departments from the legacy strings
# ---------------------------------------------------------------------------
echo
echo "== Seeding roles & permissions =="
php artisan db:seed --class=RolesAndPermissionsSeeder --force

echo "== Backfilling departments from existing users/slides =="
php artisan db:seed --class=DepartmentsSeeder --force

# ---------------------------------------------------------------------------
# 5. STOP — verify before the irreversible step
# ---------------------------------------------------------------------------
echo
echo "== Verification =="
echo "Departments created:"
php artisan tinker <<< 'foreach(\App\Department::all() as $d){ echo " - {$d->id}: {$d->name}\n"; }'

echo
echo "What to check above: one row per real department your staff actually use (IT,"
echo "Marketing, etc.) — no near-duplicates like 'IT' and 'I.T.' from inconsistent"
echo "spelling in the old data. If you see duplicates, it's safe to stop here (nothing"
echo "destructive has happened), fix the department names in the admin Departments page"
echo "or directly in the DB, then re-run this script from the top."
echo
read -p "Departments/roles look correct above — proceed with dropping the legacy columns? [y/N] " confirm2
[[ "$confirm2" == "y" || "$confirm2" == "Y" ]] || {
    echo "Stopped here on purpose. Nothing destructive has happened yet."
    echo "Fix any bad department/role data, then re-run this script — it'll resume from here."
    exit 1
}

# ---------------------------------------------------------------------------
# 6. Drop the now-redundant legacy string columns
# ---------------------------------------------------------------------------
echo
echo "== Dropping legacy department/role string columns =="
php artisan migrate --path=database/migrations/2026_07_28_010002_drop_legacy_department_string_columns.php --force

# ---------------------------------------------------------------------------
# 7. Clear caches
# ---------------------------------------------------------------------------
echo
echo "== Clearing caches =="
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo
echo "== Done =="
echo "Backup kept at: $BACKUP_FILE — safe to delete once you've confirmed everything below works."
echo
echo "Now smoke-test manually:"
echo "  1. Log in as an existing master_admin — dashboard, /users, /roles, /permissions, /departments all load?"
echo "  2. Check a lobby TV page (/ and /display/{a-real-department-slug}) — slides still play, logo shows?"
echo "  3. Log in as a non-master_admin user — do they only see their own department's slides?"
echo
echo "If something looks wrong NOW (after the script finished, not during):"
echo "  mysql -h \"$DB_HOST\" -u \"$DB_USERNAME\" ${DB_PASSWORD:+-p'<your DB password>'} \"$DB_DATABASE\" < $BACKUP_FILE"
echo "  git reset --hard $PREV_COMMIT && composer install --no-dev --optimize-autoloader"
