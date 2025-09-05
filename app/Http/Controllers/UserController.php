<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Regular user profile methods (existing)
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('name', 'email'));

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    // Admin-specific methods (new)
    public function index()
    {
        $this->authorize('user_view');

        $users = User::with(['department', 'roles'])->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('user_create');

        $departments = Department::all();
        $locations = Location::all();
        $roles = Role::all();
        return view('users.create', compact('departments', 'locations', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('user_create');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'department_id' => ['required', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'joining_date' => ['nullable', 'date'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $userData = $request->except(['profile_picture', 'roles', 'password_confirmation']);
        $userData['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        $user = User::create($userData);
        $roleNames = Role::whereIn('id', $request->roles)->pluck('name');
        $user->syncRoles($roleNames);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorize('user_view');
        return view('users.show', compact('user'));
    }

    public function editAdmin(User $user)
    {
        $this->authorize('user_edit');

        $departments = Department::all();
        $locations = Location::all();
        $roles = Role::all();
        return view('users.edit', compact('user', 'departments', 'locations', 'roles'));
    }

    public function updateAdmin(Request $request, User $user)
    {
        $this->authorize('user_edit');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'department_id' => ['required', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'joining_date' => ['nullable', 'date'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $userData = $request->except(['profile_picture', 'roles', 'password', 'password_confirmation']);

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData['profile_picture'] = $path;
        }

        $user->update($userData);
        $roleNames = Role::whereIn('id', $request->roles)->pluck('name');
        $user->syncRoles($roleNames);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('user_delete');

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
