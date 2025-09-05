@extends('components.app')

@section('title', isset($location) ? 'Edit Location' : 'Create Location')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>{{ isset($location) ? 'Edit' : 'Create' }} Location</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.locations.index') }}" class="btn btn-default">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ isset($location) ? route('admin.locations.update', $location->id) : route('admin.locations.store') }}">
      @csrf
      @if(isset($location)) @method('PUT') @endif

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" name="name" id="name" class="form-control"
              value="{{ old('name', $location->name ?? '') }}" required>
          </div>

          <div class="form-group">
            <label for="code">Code *</label>
            <input type="text" name="code" id="code" class="form-control"
              value="{{ old('code', $location->code ?? '') }}" required>
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" class="form-control"
              value="{{ old('address', $location->address ?? '') }}">
          </div>

          <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" id="city" class="form-control"
              value="{{ old('city', $location->city ?? '') }}">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="state">State</label>
            <input type="text" name="state" id="state" class="form-control"
              value="{{ old('state', $location->state ?? '') }}">
          </div>

          <div class="form-group">
            <label for="country">Country</label>
            <input type="text" name="country" id="country" class="form-control"
              value="{{ old('country', $location->country ?? '') }}">
          </div>

          <div class="form-group">
            <label for="postal_code">Postal Code</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control"
              value="{{ old('postal_code', $location->postal_code ?? '') }}">
          </div>

          <div class="form-group">
            <label for="is_active">Status</label>
            <select name="is_active" id="is_active" class="form-control">
              <option value="1" {{ old('is_active', $location->is_active ?? true) == 1 ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('is_active', $location->is_active ?? true) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>

          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control">{{ old('description', $location->description ?? '') }}</textarea>
      </div>

      <button type="submit" class="btn btn-primary">
        {{ isset($location) ? 'Update' : 'Create' }} Location
      </button>
    </form>
  </div>
</div>
@endsection