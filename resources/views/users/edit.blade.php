@extends('components.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Edit User</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.users.index') }}" class="btn btn-default">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-6">
          <h4>Basic Information</h4>

          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
          </div>

          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="text-muted">Leave blank to keep current password</small>
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
          </div>

          <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            @if($user->profile_picture)
            <div class="mb-2">
              <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="img-thumbnail" style="max-height: 100px;">
              <div class="form-check mt-2">
                <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture" class="form-check-input">
                <label for="remove_profile_picture" class="form-check-label">Remove current picture</label>
              </div>
            </div>
            @endif
            <input type="file" name="profile_picture" id="profile_picture" class="form-control-file">
          </div>
        </div>

        <div class="col-md-6">
          <h4>Additional Information</h4>

          <div class="form-group">
            <label for="employee_id">Employee ID</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control" value="{{ $user->employee_id }}">
          </div>

          <div class="form-group">
            <label for="department_id">Department *</label>
            <select name="department_id" id="department_id" class="form-control select2" required>
              <option value="">Select Department</option>
              @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}">
          </div>

          <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" class="form-control">
              <option value="">Select Gender</option>
              <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-12">
          <h4>Roles</h4>
          <div class="form-group">
            @foreach($roles as $role)
            <div class="form-check">
              <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}"
                class="form-check-input" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
              <label for="role_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Update User</button>
    </form>
  </div>
</div>
@endsection