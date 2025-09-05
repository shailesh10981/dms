@extends('components.app')

@section('title', 'Location Details')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ $location->name }} ({{ $location->code }})</h3>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Code</dt>
        <dd class="col-sm-9">{{ $location->code }}</dd>

        <dt class="col-sm-3">Address</dt>
        <dd class="col-sm-9">{{ $location->address ?? '—' }}</dd>

        <dt class="col-sm-3">City</dt>
        <dd class="col-sm-9">{{ $location->city ?? '—' }}</dd>

        <dt class="col-sm-3">State</dt>
        <dd class="col-sm-9">{{ $location->state ?? '—' }}</dd>

        <dt class="col-sm-3">Country</dt>
        <dd class="col-sm-9">{{ $location->country ?? '—' }}</dd>

        <dt class="col-sm-3">Postal Code</dt>
        <dd class="col-sm-9">{{ $location->postal_code ?? '—' }}</dd>

        <dt class="col-sm-3">Phone</dt>
        <dd class="col-sm-9">{{ $location->phone ?? '—' }}</dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9">{{ $location->email ?? '—' }}</dd>

        <dt class="col-sm-3">Active</dt>
        <dd class="col-sm-9">{{ $location->is_active ? 'Yes' : 'No' }}</dd>
      </dl>

      <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Back</a>
      <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-primary">Edit</a>
    </div>
  </div>
</div>
@endsection
