@extends('components.app')

@section('title', 'Department Management')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Department Management</h1>
    </div>
    <div class="col-sm-6 text-right">
      @can('department_create')
      <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Department
      </a>
      @endcan
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Location</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($departments as $department)
        <tr>
          <td>{{ $department->code }}</td>
          <td>{{ $department->name }}</td>
          <td>{{ $department->location->name ?? 'N/A' }}</td>
          <td>{{ Str::limit($department->description, 50) }}</td>
          <td>
            @can('department_edit')
            <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-sm btn-primary">
              <i class="fas fa-edit"></i>
            </a>
            @endcan

            @can('department_delete')
            <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" class="d-inline">
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
@endsection