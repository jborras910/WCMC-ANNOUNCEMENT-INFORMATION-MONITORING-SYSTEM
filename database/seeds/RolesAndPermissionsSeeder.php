<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Default permission set for this app, grouped by the role that gets
     * them out of the box. Both roles and permissions stay editable from
     * the admin Roles screen afterwards — this is just the starting point.
     */
    protected $rolePermissions = [
        'master_admin' => [
            'manage-users',
            'manage-roles',
            'manage-departments',
            'view-all-departments',
            'review-slides',
            'manage-slides',
            'view-all-activity-logs',
        ],
        'admin' => [
            'review-slides',
            'manage-slides',
        ],
        'faculty' => [
            'manage-slides',
        ],
    ];

    public function run()
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = collect($this->rolePermissions)->flatten()->unique();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ($this->rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }

        // Carry over each existing user's legacy `role` string column into the
        // new roles table, so nobody loses access when this cuts over.
        User::whereNotNull('role')->get()->each(function (User $user) {
            if ($user->roles()->count() === 0 && Role::where('name', $user->role)->exists()) {
                $user->assignRole($user->role);
            }
        });
    }
}
