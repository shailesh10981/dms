<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $this->authorize('department_view');

        $departments = Department::with('location')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('department_create');

        $locations = Location::all();
        return view('departments.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $this->authorize('department_create');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string'
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully');
    }

    public function show(Department $department)
    {
        $this->authorize('department_view');

        $department->load('location', 'users');
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $this->authorize('department_edit');

        $locations = Location::all();
        return view('departments.edit', compact('department', 'locations'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('department_edit');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string'
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $this->authorize('department_delete');

        if ($department->users()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete department with assigned users');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}
