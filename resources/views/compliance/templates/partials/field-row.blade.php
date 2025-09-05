<div class="field-row card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-5">
        <div class="form-group">
          <label>Field Label *</label>
          <input type="text" name="fields[{{ $index }}][label]"
            class="form-control @error('fields.'.$index.'.label') is-invalid @enderror"
            value="{{ old('fields.'.$index.'.label', $field->label ?? '') }}" required>
          @error('fields.'.$index.'.label')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Field Type *</label>
          <select name="fields[{{ $index }}][field_type]"
            class="form-control field-type-select @error('fields.'.$index.'.field_type') is-invalid @enderror"
            onchange="handleFieldTypeChange(this)" required>
            @foreach($fieldTypes as $value => $label)
            <option value="{{ $value }}"
              {{ old('fields.'.$index.'.field_type', $field->field_type ?? '') == $value ? 'selected' : '' }}>
              {{ $label }}
            </option>
            @endforeach
          </select>
          @error('fields.'.$index.'.field_type')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <div class="custom-control custom-switch mt-4">
            <input type="checkbox" class="custom-control-input"
              id="fields_{{ $index }}_is_required"
              name="fields[{{ $index }}][is_required]" value="1"
              {{ old('fields.'.$index.'.is_required', $field->is_required ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="fields_{{ $index }}_is_required">Required</label>
          </div>
        </div>
      </div>
      <div class="col-md-1 text-right">
        <button class="btn btn-sm btn-danger remove-field mt-4">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>

    <div class="field-options-container" @style([ 'display: block'=> old('fields.'.$index.'.field_type', $field->field_type ?? '') === 'select',
      'display: none' => old('fields.'.$index.'.field_type', $field->field_type ?? '') !== 'select'
      ])>
      <div class="form-group">
        <label>Dropdown Options (one per line)</label>
        <textarea name="fields[{{ $index }}][options]"
          class="form-control @error('fields.'.$index.'.options') is-invalid @enderror"
          rows="3">{{ old('fields.'.$index.'.options', isset($field->options) ? implode("\n", is_array($field->options) ? $field->options : explode("\n", $field->options)) : '') }}
        </textarea>
        <small class="form-text text-muted">Enter each option on a new line</small>
        @error('fields.'.$index.'.options')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>
    </div>

    <div class="field-validation-container" style="display: {{ in_array(old('fields.'.$index.'.field_type', $field->field_type ?? ''), ['number', 'date']) ? 'block' : 'none' }};">
      <div class="form-group">
        <label>Validation Rules</label>
        <select name="fields[{{ $index }}][validation_rules][]"
          class="form-control select2 @error('fields.'.$index.'.validation_rules') is-invalid @enderror"
          multiple>
          @php
          $currentRules = old('fields.'.$index.'.validation_rules', $field->validation_rules ?? []);
          $fieldType = old('fields.'.$index.'.field_type', $field->field_type ?? '');
          @endphp

          @if($fieldType === 'number')
          <option value="min:0" {{ in_array('min:0', $currentRules) ? 'selected' : '' }}>Minimum value: 0</option>
          <option value="max:100" {{ in_array('max:100', $currentRules) ? 'selected' : '' }}>Maximum value: 100</option>
          <option value="integer" {{ in_array('integer', $currentRules) ? 'selected' : '' }}>Whole numbers only</option>
          @elseif($fieldType === 'date')
          <option value="after:today" {{ in_array('after:today', $currentRules) ? 'selected' : '' }}>Must be future date</option>
          <option value="before:today" {{ in_array('before:today', $currentRules) ? 'selected' : '' }}>Must be past date</option>
          @endif
        </select>
        @error('fields.'.$index.'.validation_rules')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>
    </div>

    @if(isset($field->id))
    <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field->id }}">
    @endif
  </div>
</div>