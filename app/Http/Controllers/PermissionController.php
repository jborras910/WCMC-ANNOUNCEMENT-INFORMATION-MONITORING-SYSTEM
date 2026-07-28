<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $this->data['permissions'] = Permission::withCount('roles')->orderBy('name')->get();

        return view('admin.permissions', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        $this->logActivity('created the "' . $request->name . '" permission');

        return redirect()->route('admin.permissions')->with('success', 'Permission created successfully');
    }

    public function destroyPermission(Permission $permission)
    {
        $permissionName = $permission->name;
        $permission->delete();

        $this->logActivity('deleted the "' . $permissionName . '" permission');

        return redirect()->route('admin.permissions')->with('success', 'Permission deleted successfully');
    }
}
