@extends('components.app')

@section('title', 'Edit Compliance Report')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Edit Compliance Report</h2>
      <h5 class="text-muted">Status:
        <span class="badge 
                    @if($report->status === 'draft') bg-secondary
                    @elseif($report->status === 'submitted') bg-primary
                    @elseif($report->status === 'approved') bg-success
                    @elseif($report->status === 'rejected') bg-danger @endif">
          {{ ucfirst($report->status) }}
        </span>
      </h5>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('compliance.reports.show', $report) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Report
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('compliance.reports.update', $report) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="description">Description</label>
          <textarea class="form-control @error('description') is-invalid @enderror"
            id="description" name="description" rows="2">{{ old('description', $report->description) }}</textarea>
          @error('description')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <hr>

        <h4>Report Data</h4>

        @foreach($fields as $field)
        <div class="form-group mb-4">
          <label for="field_{{ $field->id }}">
            {{ $field->label }}
            @if($field->is_required)
            <span class="text-danger">*</span>
            @endif
          </label>

          @if($field->field_type === 'text')
          <input type="text"
            class="form-control @error('fields.' . $field->label) is-invalid @enderror"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->label }}]"
            value="{{ old('fields.' . $field->label, $report->getFieldValue($field->label)) }}"
            @if($field->is_required) required @endif>

          @elseif($field->field_type === 'number')
          <input type="number"
            class="form-control @error('fields.' . $field->label) is-invalid @enderror"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->label }}]"
            value="{{ old('fields.' . $field->label, $report->getFieldValue($field->label)) }}"
            @if($field->is_required) required @endif>

          @elseif($field->field_type === 'date')
          <input type="date"
            class="form-control @error('fields.' . $field->label) is-invalid @enderror"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->label }}]"
            value="{{ old('fields.' . $field->label, $report->getFieldValue($field->label)) }}"
            @if($field->is_required) required @endif>

          @elseif($field->field_type === 'select')
          <select class="form-control @error('fields.' . $field->label) is-invalid @enderror"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->label }}]"
            @if($field->is_required) required @endif>
            <option value="">Select an option</option>
            @foreach(explode("\n", $field->options) as $option)
            <option value="{{ trim($option) }}"
              {{ old('fields.' . $field->label, $report->getFieldValue($field->label)) == trim($option) ? 'selected' : '' }}>
              {{ trim($option) }}
            </option>
            @endforeach
          </select>

          @elseif($field->field_type === 'checkbox')
          <div class="custom-control custom-checkbox">
            <input type="checkbox"
              class="custom-control-input @error('fields.' . $field->label) is-invalid @enderror"
              id="field_{{ $field->id }}"
              name="fields[{{ $field->label }}]"
              value="1"
              {{ old('fields.' . $field->label, $report->getFieldValue($field->label)) ? 'checked' : '' }}>
            <label class="custom-control-label" for="field_{{ $field->id }}">Yes</label>
          </div>

          @elseif($field->field_type === 'textarea')
          <textarea class="form-control @error('fields.' . $field->label) is-invalid @enderror"
            id="field_{{ $field->id }}"
            name="fields[{{ $field->label }}]"
            rows="3"
            @if($field->is_required) required @endif>{{ old('fields.' . $field->label, $report->getFieldValue($field->label)) }}</textarea>
          @endif

          @error('fields.' . $field->label)
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        @endforeach

        <div class="form-group mt-4">
          @if($report->status === 'draft')
          <button type="submit" name="save" value="draft" class="btn btn-secondary">
            <i class="fas fa-save"></i> Save as Draft
          </button>
          <button type="submit" name="submit" value="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Submit Report
          </button>
          @else
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Report
          </button>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@endsection