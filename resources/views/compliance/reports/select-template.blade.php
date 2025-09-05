@extends('components.app')

@section('title', 'Select Template')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Select Template</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('compliance.reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      @if($templates->isEmpty())
      <div class="alert alert-info">
        No active templates available for your department.
      </div>
      @else
      <div class="row">
        @foreach($templates as $template)
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">{{ $template->name }}</h5>
            </div>
            <div class="card-body">
              <p>{{ $template->description }}</p>
              <ul class="list-unstyled">
                <li><strong>Department:</strong> {{ $template->department->name }}</li>
                <li><strong>Frequency:</strong> {{ ucfirst($template->frequency) }}</li>
                <li><strong>Fields:</strong> {{ $template->fields->count() }}</li>
              </ul>
            </div>
            <div class="card-footer">
              <a href="{{ route('compliance.reports.create-from-template', $template) }}"
                class="btn btn-primary btn-block">
                <i class="fas fa-plus"></i> Create Report
              </a>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>
</div>
@endsection