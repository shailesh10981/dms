<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('role_view');

        $roles = Role::with(['permissions'])->withCount('users')->get();
        // $permissions = Permission::all()->groupBy('module');
        $permissions = Permission::all(); // don't group here
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('role_create');

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role = Role::create(['name' => $request->name]);

        // Convert permission IDs to Permission models
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('role_edit');

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        $role->update(['name' => $request->name]);

        // Fix: Convert IDs to permission models
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('role_delete');

        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete the admin role.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
