@extends('components.app')

@section('title', 'Department Details')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ $department->name }} ({{ $department->code }})</h3>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Code</dt>
        <dd class="col-sm-9">{{ $department->code }}</dd>

        <dt class="col-sm-3">Location</dt>
        <dd class="col-sm-9">{{ $department->location->name ?? '—' }}</dd>

        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">{{ $department->description ?? '—' }}</dd>
      </dl>

      <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Back</a>
      <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary">Edit</a>
    </div>
  </div>
</div>
@endsection
