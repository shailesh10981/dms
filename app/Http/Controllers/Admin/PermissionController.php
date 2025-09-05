<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $this->authorize('permission_view');

        $permissions = Permission::all()->groupBy('module');
        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('permission_create');

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'module' => ['required', 'string', 'max:255']
        ]);

        Permission::create($request->only(['name', 'module']));
        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('permission_edit');

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'module' => ['required', 'string', 'max:255']
        ]);

        $permission->update($request->only(['name', 'module']));
        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('permission_delete');

        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
