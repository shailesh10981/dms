@extends('components.app')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Create New User</h1>
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
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">
        <div class="col-md-6">
          <h4>Basic Information</h4>

          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" name="password" id="password"
              class="form-control @error('password') is-invalid @enderror" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password *</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture"
              class="form-control-file @error('profile_picture') is-invalid @enderror">
            @error('profile_picture') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="col-md-6">
          <h4>Additional Information</h4>

          <div class="form-group">
            <label for="employee_id">Employee ID</label>
            <input type="text" name="employee_id" id="employee_id"
              class="form-control @error('employee_id') is-invalid @enderror"
              value="{{ old('employee_id') }}">
            @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="department_id">Department *</label>
            <select name="department_id" id="department_id"
              class="form-control select2 @error('department_id') is-invalid @enderror" required>
              <option value="">Select Department</option>
              @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
              @endforeach
            </select>
            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone"
              class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone') }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
              <option value="">Select Gender</option>
              <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
              <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                class="form-check-input"
                {{ is_array(old('roles')) && in_array($role->id, old('roles')) ? 'checked' : '' }}>
              <label for="role_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
            </div>
            @endforeach
            @error('roles') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Create User</button>
    </form>
  </div>
</div>
@endsection