@extends('components.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>User Management</h1>
    </div>
    <div class="col-sm-6 text-right">
      @can('user_create')
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New User
      </a>
      @endcan
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Roles</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>{{ $user->id ?? 'N/A' }}</td>
            <td>
              <div class="d-flex align-items-center">
                @if($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                  alt="{{ $user->name }}"
                  class="img-circle img-size-32 mr-2">
                @else
                <div class="img-circle img-size-32 bg-secondary d-flex align-items-center justify-content-center mr-2">
                  <i class="fas fa-user text-white"></i>
                </div>
                @endif
                {{ $user->name }}
              </div>
            </td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->department->name ?? 'N/A' }}</td>
            <td>
              @foreach($user->roles as $role)
              <span class="badge badge-primary">{{ $role->name }}</span>
              @endforeach
            </td>
            <td>
              <span class="badge badge-success">Active</span>
            </td>
            <td>
              @can('user_view')
              <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              @endcan

              @can('user_edit')
              <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              @endcan

              @can('user_delete')
              <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
              @endcan
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection