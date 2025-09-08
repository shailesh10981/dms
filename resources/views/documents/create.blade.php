@extends('components.app')

@section('title', 'Upload Document')

@section('content')
<div class="container-fluid">


  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group row">
          <label for="title" class="col-md-3 col-form-label">Title *</label>
          <div class="col-md-9">
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="description" class="col-md-3 col-form-label">Description</label>
          <div class="col-md-9">
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="department_id" class="col-md-3 col-form-label">Department *</label>
          <div class="col-md-9">
            <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
              <option value="">Select Department</option>
              @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
              @endforeach
            </select>
            @error('department_id')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="location_id" class="col-md-3 col-form-label">Location</label>
          <div class="col-md-9">
            <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
              <option value="">Select Location</option>
              @foreach($locations as $location)
              <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                {{ $location->name }}
              </option>
              @endforeach
            </select>
            @error('location_id')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="project_id" class="col-md-3 col-form-label">Project</label>
          <div class="col-md-9">
            <select class="form-control @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
              <option value="">Select Project</option>
              @foreach($projects as $project)
              <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                {{ $project->name }} ({{ $project->code }})
              </option>
              @endforeach
            </select>
            @error('project_id')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="expiry_date" class="col-md-3 col-form-label">Expiry Date</label>
          <div class="col-md-9">
            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d') }}">
            @error('expiry_date')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="visibility" class="col-md-3 col-form-label">Visibility *</label>
          <div class="col-md-9">
            <select class="form-control @error('visibility') is-invalid @enderror" id="visibility" name="visibility" required>
              <option value="Private" {{ old('visibility')=='Private'?'selected':'' }}>Private</option>
              <option value="Public" {{ old('visibility')=='Public'?'selected':'' }}>Public</option>
              <option value="Publish" {{ old('visibility')=='Publish'?'selected':'' }}>Publish</option>
            </select>
            @error('visibility')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 col-form-label">Approval Workflow</label>
          <div class="col-md-9">
            <select class="form-control select2" name="approver_ids[]" multiple data-placeholder="Select approvers in order">
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ collect(old('approver_ids', []))->contains($u->id) ? 'selected' : '' }}>
                  {{ $u->name }} @if($u->department) ({{ $u->department->code }}) @endif
                </option>
              @endforeach
            </select>
            <small class="text-muted">Selection order defines workflow order. Default HOD will be used if left empty.</small>
          </div>
        </div>

        <div class="form-group row">
          <label for="document" class="col-md-3 col-form-label">Document File *</label>
          <div class="col-md-9">
            <div class="custom-file">
              <input type="file" class="custom-file-input @error('document') is-invalid @enderror" id="document" name="document" required>
              <label class="custom-file-label" for="document">Choose file (max: 10MB)</label>
              @error('document')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group row mb-0">
          <div class="col-md-9 offset-md-3">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-upload"></i> Upload Document
            </button>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">
              Cancel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@endpush
@section('scripts')
<script>
  // Show the file name when a file is selected
  document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = document.getElementById("document").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
  });
</script>
@endsection