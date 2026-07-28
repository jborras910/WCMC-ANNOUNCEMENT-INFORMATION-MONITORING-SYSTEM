#!/usr/bin/env bash
#
# One-time production rollout for the RBAC + Departments release.
# Run this FROM THE PROJECT ROOT on the prod server (Git Bash).
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

set -euo pipefail
cd "$(dirname "$0")/.."

echo "== WCMC AIMS — RBAC + Departments deploy =="
echo "Working directory: $(pwd)"
echo

read -p "This will modify the production database. Continue? [y/N] " confirm
[[ "$confirm" == "y" || "$confirm" == "Y" ]] || { echo "Aborted."; exit 1; }

# ---------------------------------------------------------------------------
# 1. Backup the database first. Non-negotiable.
# ---------------------------------------------------------------------------
if [ ! -f .env ]; then
    echo "No .env found here — run this from the Laravel project root."
    exit 1
fi

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
echo "Backup OK ($(du -h "$BACKUP_FILE" | cut -f1)). Keep this file until you've verified the deploy."
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
echo "Spot-check a few real users below — confirm their department and role look right"
echo "before continuing. This is your last chance before the legacy columns are dropped."
echo
read -p "Departments/roles look correct above — proceed with dropping the legacy columns? [y/N] " confirm2
[[ "$confirm2" == "y" || "$confirm2" == "Y" ]] || {
    echo "Stopped here on purpose. Nothing destructive has happened yet."
    echo "Fix any bad department/role data directly in the DB, then re-run this script — it'll resume from here."
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
echo "Backup kept at: $BACKUP_FILE"
echo
echo "Now smoke-test manually:"
echo "  1. Log in as an existing master_admin — dashboard, /users, /roles, /permissions, /departments all load?"
echo "  2. Check a lobby TV page (/ and /display/{a-real-department-slug}) — slides still play, logo shows?"
echo "  3. Log in as a non-master_admin user — do they only see their own department's slides?"
echo
echo "If anything looks wrong: restore $BACKUP_FILE and git reset --hard to the previous commit."
