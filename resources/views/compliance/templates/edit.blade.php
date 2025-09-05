@extends('components.app')

@section('title', 'Edit Compliance Template')

@push('styles')
<style>
  .field-options-container,
  .field-validation-container {
    display: none;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
  <h2>Edit Compliance Template</h2>

  <form method="POST" action="{{ route('compliance.templates.update', $template->id) }}">
    @csrf
    @method('PUT')

    <!-- Template Info -->
    <div class="mb-3">
      <label for="name" class="form-label">Template Name</label>
      <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $template->name) }}" required>
    </div>

    <div class="mb-3">
      <label for="department_id" class="form-label">Department</label>
      <select name="department_id" id="department_id" class="form-control" required>
        <option value="">Select Department</option>
        @foreach($departments as $department)
        <option value="{{ $department->id }}"
          {{ $department->id == old('department_id', $template->department_id) ? 'selected' : '' }}>
          {{ $department->name }}
        </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label for="frequency" class="form-label">Frequency</label>
      <select name="frequency" id="frequency" class="form-control" required>
        <option value="daily" {{ old('frequency', $template->frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
        <option value="weekly" {{ old('frequency', $template->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
        <option value="monthly" {{ old('frequency', $template->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
      </select>
    </div>

    <!-- Fields Section -->
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Template Fields</h5>
        <button type="button" class="btn btn-sm btn-success" id="add-field">
          <i class="fas fa-plus"></i> Add Field
        </button>
      </div>
      <div class="card-body" id="fields-container">
        @foreach($template->fields as $index => $field)
        @include('compliance.templates.partials.field-row', ['index' => $index, 'field' => $field])
        @endforeach
      </div>
    </div>

    <!-- Status -->
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
        {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">
        Active
      </label>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary">Update Template</button>
      <a href="{{ route('compliance.templates.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
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