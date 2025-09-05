@extends('components.app')

@section('title', 'Create New Version')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h3>Create New Version of: {{ $document->title }}</h3>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('documents.show', $document->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Document
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('documents.store-version', $document->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group row">
          <label for="title" class="col-md-3 col-form-label">Title *</label>
          <div class="col-md-9">
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $document->title) }}" required>
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
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
        </div>

        <div class="form-group row">
          <label for="document" class="col-md-3 col-form-label">New File *</label>
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
            <small class="form-text text-muted">Current file: {{ $document->file_name }}</small>
          </div>
        </div>

        <div class="form-group row mb-0">
          <div class="col-md-9 offset-md-3">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create New Version
            </button>
            <a href="{{ route('documents.show', $document->id) }}" class="btn btn-secondary">
              Cancel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

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