<div class="field-row card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-5">
        <div class="form-group">
          <label>Field Label *</label>
          <input type="text" name="fields[<?php echo e($index); ?>][label]"
            class="form-control <?php $__errorArgs = ['fields.'.$index.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('fields.'.$index.'.label', $field->label ?? '')); ?>" required>
          <?php $__errorArgs = ['fields.'.$index.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <span class="invalid-feedback" role="alert">
            <strong><?php echo e($message); ?></strong>
          </span>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Field Type *</label>
          <select name="fields[<?php echo e($index); ?>][field_type]"
            class="form-control field-type-select <?php $__errorArgs = ['fields.'.$index.'.field_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            onchange="handleFieldTypeChange(this)" required>
            <?php $__currentLoopData = $fieldTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($value); ?>"
              <?php echo e(old('fields.'.$index.'.field_type', $field->field_type ?? '') == $value ? 'selected' : ''); ?>>
              <?php echo e($label); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['fields.'.$index.'.field_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <span class="invalid-feedback" role="alert">
            <strong><?php echo e($message); ?></strong>
          </span>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <div class="custom-control custom-switch mt-4">
            <input type="checkbox" class="custom-control-input"
              id="fields_<?php echo e($index); ?>_is_required"
              name="fields[<?php echo e($index); ?>][is_required]" value="1"
              <?php echo e(old('fields.'.$index.'.is_required', $field->is_required ?? false) ? 'checked' : ''); ?>>
            <label class="custom-control-label" for="fields_<?php echo e($index); ?>_is_required">Required</label>
          </div>
        </div>
      </div>
      <div class="col-md-1 text-right">
        <button class="btn btn-sm btn-danger remove-field mt-4">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>

    <div class="field-options-container" style="<?php echo \Illuminate\Support\Arr::toCssStyles([ 'display: block'=> old('fields.'.$index.'.field_type', $field->field_type ?? '') === 'select',
      'display: none' => old('fields.'.$index.'.field_type', $field->field_type ?? '') !== 'select'
      ]) ?>">
      <div class="form-group">
        <label>Dropdown Options (one per line)</label>
        <textarea name="fields[<?php echo e($index); ?>][options]"
          class="form-control <?php $__errorArgs = ['fields.'.$index.'.options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
          rows="3"><?php echo e(old('fields.'.$index.'.options', isset($field->options) ? implode("\n", is_array($field->options) ? $field->options : explode("\n", $field->options)) : '')); ?>

        </textarea>
        <small class="form-text text-muted">Enter each option on a new line</small>
        <?php $__errorArgs = ['fields.'.$index.'.options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="invalid-feedback" role="alert">
          <strong><?php echo e($message); ?></strong>
        </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <div class="field-validation-container" style="display: <?php echo e(in_array(old('fields.'.$index.'.field_type', $field->field_type ?? ''), ['number', 'date']) ? 'block' : 'none'); ?>;">
      <div class="form-group">
        <label>Validation Rules</label>
        <select name="fields[<?php echo e($index); ?>][validation_rules][]"
          class="form-control select2 <?php $__errorArgs = ['fields.'.$index.'.validation_rules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
          multiple>
          <?php
          $currentRules = old('fields.'.$index.'.validation_rules', $field->validation_rules ?? []);
          $fieldType = old('fields.'.$index.'.field_type', $field->field_type ?? '');
          ?>

          <?php if($fieldType === 'number'): ?>
          <option value="min:0" <?php echo e(in_array('min:0', $currentRules) ? 'selected' : ''); ?>>Minimum value: 0</option>
          <option value="max:100" <?php echo e(in_array('max:100', $currentRules) ? 'selected' : ''); ?>>Maximum value: 100</option>
          <option value="integer" <?php echo e(in_array('integer', $currentRules) ? 'selected' : ''); ?>>Whole numbers only</option>
          <?php elseif($fieldType === 'date'): ?>
          <option value="after:today" <?php echo e(in_array('after:today', $currentRules) ? 'selected' : ''); ?>>Must be future date</option>
          <option value="before:today" <?php echo e(in_array('before:today', $currentRules) ? 'selected' : ''); ?>>Must be past date</option>
          <?php endif; ?>
        </select>
        <?php $__errorArgs = ['fields.'.$index.'.validation_rules'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="invalid-feedback" role="alert">
          <strong><?php echo e($message); ?></strong>
        </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <?php if(isset($field->id)): ?>
    <input type="hidden" name="fields[<?php echo e($index); ?>][id]" value="<?php echo e($field->id); ?>">
    <?php endif; ?>
  </div>
</div><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/compliance/templates/partials/field-row.blade.php ENDPATH**/ ?>