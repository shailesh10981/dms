@extends('components.app')

@section('title', 'Location Management')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Location Management</h1>
    </div>
    <div class="col-sm-6 text-right">
      @can('location_create')
      <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Location
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
          <th>Address</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($locations as $location)
        <tr>
          <td>{{ $location->code }}</td>
          <td>{{ $location->name }}</td>
          <td>{{ Str::limit($location->address, 30) }}</td>
          <td>
            @if($location->is_active)
            <span class="badge badge-success">Active</span>
            @else
            <span class="badge badge-danger">Inactive</span>
            @endif
          </td>
          <td>
            @can('location_edit')
            <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-sm btn-primary">
              <i class="fas fa-edit"></i>
            </a>
            @endcan

            @can('location_delete')
            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="d-inline">
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