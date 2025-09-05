@extends('components.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>User Details</h1>
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
    <div class="row">
      <div class="col-md-4 text-center">
        @if($user->profile_picture)
        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="max-height: 200px;">
        @else
        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; margin: 0 auto;">
          <i class="fas fa-user text-white" style="font-size: 5rem;"></i>
        </div>
        @endif

        <h3>{{ $user->name }}</h3>
        <p class="text-muted">{{ $user->email }}</p>

        @if($user->hasRole('admin'))
        <span class="badge badge-danger">Administrator</span>
        @else
        @foreach($user->roles as $role)
        <span class="badge badge-primary">{{ $role->name }}</span>
        @endforeach
        @endif
      </div>

      <div class="col-md-8">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Employee ID</label>
              <p>{{ $user->employee_id ?? 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Department</label>
              <p>{{ $user->department->name ?? 'N/A' }}</p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Phone</label>
              <p>{{ $user->phone ?? 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Gender</label>
              <p>{{ ucfirst($user->gender) ?? 'N/A' }}</p>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Joining Date</label>
              <p>{{ $user->joining_date ? $user->joining_date->format('M d, Y') : 'N/A' }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Birth Date</label>
              <p>{{ $user->birth_date ? $user->birth_date->format('M d, Y') : 'N/A' }}</p>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Address</label>
          <p>{{ $user->address ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
          <label>Account Status</label>
          <p>
            <span class="badge badge-success">Active</span>
            <small class="text-muted">Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer">
    <div class="text-right">
      @can('user_edit')
      <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit User
      </a>
      @endcan
    </div>
  </div>
</div>
@endsection