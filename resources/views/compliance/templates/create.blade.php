@extends('components.app')

@section('title', 'Create Compliance Template')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Create Compliance Template</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('compliance.templates.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Templates
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('compliance.templates.store') }}">
        @csrf

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Template Name *</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name') }}" required>
              @error('name')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="department_id">Department *</label>
              <select class="form-control @error('department_id') is-invalid @enderror"
                id="department_id" name="department_id" required>
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
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea class="form-control @error('description') is-invalid @enderror"
            id="description" name="description" rows="2">{{ old('description') }}</textarea>
          @error('description')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="frequency">Frequency *</label>
              <select class="form-control @error('frequency') is-invalid @enderror"
                id="frequency" name="frequency" required>
                <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                <option value="adhoc" {{ old('frequency') == 'adhoc' ? 'selected' : '' }}>Ad Hoc</option>
              </select>
              @error('frequency')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <div class="custom-control custom-switch mt-4">
                <input type="checkbox" class="custom-control-input" id="is_active"
                  name="is_active" value="1" {{ old('is_active', false) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">Active Template</label>
              </div>
            </div>
          </div>
        </div>

        <hr>

        <h4>Template Fields</h4>
        <div id="fields-container">
          @foreach(old('fields', []) as $index => $field)
          @include('compliance.templates.partials.field-row', [
          'index' => $index,
          'field' => (object)$field,
          'fieldTypes' => $fieldTypes,
          'errors' => $errors
          ])
          @endforeach
        </div>

        <button type="button" id="add-field" class="btn btn-sm btn-secondary mt-2">
          <i class="fas fa-plus"></i> Add Field
        </button>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Template
          </button>
          <a href="{{ route('compliance.templates.index') }}" class="btn btn-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .field-row {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 5px;
    background: #f9f9f9;
  }

  .remove-field {
    cursor: pointer;
  }
</style>
@endpush

@push('scripts')
<script>
  window.FIELD_ROW_URL = "{{ route('compliance.templates.field-row') }}";
  const initialFields = @json(old('fields', []));
  let fieldIndex = initialFields.length;

  document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('add-field');
    if (addButton) {
      addButton.addEventListener('click', addNewField);
    }

    document.querySelectorAll('.field-row').forEach(initField);
  });
  // Add New Field Function
  function addNewField() {
    fetch(`/compliance-templates/field-row?index=${fieldIndex}`)
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
      })
      .then(html => {
        const container = document.getElementById('fields-container');
        if (container) {
          container.insertAdjacentHTML('beforeend', html);
          initField(container.lastElementChild);
          fieldIndex++;
        }
      })
      .catch(error => console.error('Error adding field:', error));
  }

  // Initialize Field Function
  function initField(fieldElement) {
    if (!fieldElement) return;

    // Field Type Change Handler
    const typeSelect = fieldElement.querySelector('.field-type-select');
    if (typeSelect) {
      typeSelect.addEventListener('change', handleFieldTypeChange);
      handleFieldTypeChange({
        target: typeSelect
      });
    }

    // Remove Field Button
    const removeBtn = fieldElement.querySelector('.remove-field');
    if (removeBtn) {
      removeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fieldElement.remove();
      });
    }
  }

  // Handle Field Type Changes
  function handleFieldTypeChange(event) {
    const select = event.target;
    if (!select) return;

    const row = select.closest('.field-row');
    if (!row) return;

    const optionsContainer = row.querySelector('.field-options-container');
    const validationContainer = row.querySelector('.field-validation-container');

    if (optionsContainer) {
      optionsContainer.style.display = select.value === 'select' ? 'block' : 'none';
    }

    if (validationContainer) {
      validationContainer.style.display =
        (select.value === 'number' || select.value === 'date') ? 'block' : 'none';
    }
  }
</script>
@endpush