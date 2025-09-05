@extends('components.app')

@section('title', isset($department) ? 'Edit Department' : 'Create Department')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>{{ isset($department) ? 'Edit' : 'Create' }} Department</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.departments.index') }}" class="btn btn-default">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>
  </div>
</div>

@include('components.alerts') {{-- Include validation/success/error messages --}}

<div class="card">
  <div class="card-body">
    <form method="POST"
      action="{{ isset($department) ? route('admin.departments.update', $department->id) : route('admin.departments.store') }}">
      @csrf
      @if(isset($department))
      @method('PUT')
      @endif

      <div class="form-group">
        <label for="name">Name *</label>
        <input type="text" name="name" id="name" class="form-control"
          value="{{ old('name', $department->name ?? '') }}" required>
      </div>

      <div class="form-group">
        <label for="code">Code *</label>
        <input type="text" name="code" id="code" class="form-control"
          value="{{ old('code', $department->code ?? '') }}" required>
      </div>

      <div class="form-group">
        <label for="location_id">Location *</label>
        <select name="location_id" id="location_id" class="form-control select2" required>
          <option value="">Select Location</option>
          @foreach($locations as $location)
          <option value="{{ $location->id }}"
            {{ old('location_id', $department->location_id ?? '') == $location->id ? 'selected' : '' }}>
            {{ $location->name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control"
          rows="4">{{ old('description', $department->description ?? '') }}</textarea>
      </div>

      <button type="submit" class="btn btn-primary">
        {{ isset($department) ? 'Update' : 'Create' }} Department
      </button>
    </form>
  </div>
</div>
@endsection