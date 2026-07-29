<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $this->data['roles'] = Role::with('permissions')->orderBy('name')->get();
        $this->data['permissions'] = Permission::withCount('roles')->orderBy('name')->get();

        return view('admin.roles', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $permissions = $request->permissions ?? [];
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $suffix = $permissions ? ' (permissions: ' . implode(', ', $permissions) . ')' : '';
        $this->logActivity('created the "' . $role->name . '" role' . $suffix, 'role');

        return redirect()->route('admin.roles')->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $before = $role->permissions->pluck('name')->toArray();
        $after = $request->permissions ?? [];
        $role->syncPermissions($after);

        $diff = $this->describeSetChanges('permissions', $before, $after);
        $this->logActivity(
            'updated the "' . $role->name . '" role' . ($diff ? ' (' . $diff . ')' : ' (no change)'),
            'role'
        );

        return redirect()->route('admin.roles')->with('success', 'Role permissions updated successfully');
    }

    public function destroyRole(Role $role)
    {
        if ($role->name === User::ROLE_MASTER_ADMIN) {
            return redirect()->route('admin.roles')->with('error', 'The master_admin role cannot be deleted.');
        }

        $roleName = $role->name;
        $role->delete();

        $this->logActivity('deleted the "' . $roleName . '" role', 'role');

        return redirect()->route('admin.roles')->with('success', 'Role deleted successfully');
    }
}
