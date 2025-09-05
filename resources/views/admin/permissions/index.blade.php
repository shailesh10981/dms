@extends('components.app')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="card-title">All Permissions</h3>
    </div>
    <div class="card-body">
      @if($permissions->isEmpty())
        <p class="text-muted">No permissions found.</p>
      @else
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Module</th>
                <th>Guard</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              @foreach($permissions as $module => $items)
                @foreach($items as $perm)
                  <tr>
                    <td>{{ $perm->name }}</td>
                    <td>{{ $module ?: '—' }}</td>
                    <td>{{ $perm->guard_name }}</td>
                    <td>{{ $perm->created_at }}</td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
