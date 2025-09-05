@extends('components.app')

@section('title', 'View Compliance Template')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Template: {{ $template->name }}</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('compliance.templates.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Templates
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <p><strong>Department:</strong> {{ $template->department->name }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Frequency:</strong> {{ ucfirst($template->frequency) }}</p>
        </div>
      </div>

      <p><strong>Description:</strong> {{ $template->description ?? 'N/A' }}</p>

      <hr>

      <h4>Fields</h4>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Label</th>
              <th>Type</th>
              <th>Required</th>
              <th>Options</th>
              <th>Validation Rules</th>
            </tr>
          </thead>
          <tbody>
            @foreach($template->fields as $field)
            <tr>
              <td>{{ $field->label }}</td>
              <td>{{ $fieldTypes[$field->field_type] ?? $field->field_type }}</td>
              <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
              <td>{{ $field->field_type === 'select' ? implode(', ', $field->options ?? []) : 'N/A' }}</td>
              <td>{{ implode(', ', $field->validation_rules ?? []) ?: 'None' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection