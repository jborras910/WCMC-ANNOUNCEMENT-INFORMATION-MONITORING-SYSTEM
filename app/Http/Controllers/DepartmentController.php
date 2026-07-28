<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $this->data['departments'] = Department::withCount(['users', 'slides'])->orderBy('name')->get();

        return view('admin.departments', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->name);

        Department::create(['name' => $request->name, 'slug' => $slug]);

        $this->logActivity('created the "' . $request->name . '" department');

        return redirect()->route('admin.departments')->with('success', 'Department created successfully');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
        ]);

        $this->logActivity('renamed a department to "' . $request->name . '"');

        return redirect()->route('admin.departments')->with('success', 'Department updated successfully');
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->users()->exists() || $department->slides()->exists()) {
            return redirect()->route('admin.departments')
                ->with('error', 'Reassign or remove its users and slides before deleting this department.');
        }

        $name = $department->name;
        $department->delete();

        $this->logActivity('deleted the "' . $name . '" department');

        return redirect()->route('admin.departments')->with('success', 'Department deleted successfully');
    }
}
