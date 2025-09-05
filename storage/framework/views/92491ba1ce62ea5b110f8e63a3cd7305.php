

<?php $__env->startSection('title', 'Create Compliance Template'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Create Compliance Template</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="<?php echo e(route('compliance.templates.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Templates
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="<?php echo e(route('compliance.templates.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Template Name *</label>
              <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                id="name" name="name" value="<?php echo e(old('name')); ?>" required>
              <?php $__errorArgs = ['name'];
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
          <div class="col-md-6">
            <div class="form-group">
              <label for="department_id">Department *</label>
              <select class="form-control <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($department->id); ?>" <?php echo e(old('department_id') == $department->id ? 'selected' : ''); ?>>
                  <?php echo e($department->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['department_id'];
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
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            id="description" name="description" rows="2"><?php echo e(old('description')); ?></textarea>
          <?php $__errorArgs = ['description'];
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

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="frequency">Frequency *</label>
              <select class="form-control <?php $__errorArgs = ['frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                id="frequency" name="frequency" required>
                <option value="daily" <?php echo e(old('frequency') == 'daily' ? 'selected' : ''); ?>>Daily</option>
                <option value="weekly" <?php echo e(old('frequency') == 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                <option value="monthly" <?php echo e(old('frequency') == 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                <option value="quarterly" <?php echo e(old('frequency') == 'quarterly' ? 'selected' : ''); ?>>Quarterly</option>
                <option value="yearly" <?php echo e(old('frequency') == 'yearly' ? 'selected' : ''); ?>>Yearly</option>
                <option value="adhoc" <?php echo e(old('frequency') == 'adhoc' ? 'selected' : ''); ?>>Ad Hoc</option>
              </select>
              <?php $__errorArgs = ['frequency'];
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
          <div class="col-md-6">
            <div class="form-group">
              <div class="custom-control custom-switch mt-4">
                <input type="checkbox" class="custom-control-input" id="is_active"
                  name="is_active" value="1" <?php echo e(old('is_active', false) ? 'checked' : ''); ?>>
                <label class="custom-control-label" for="is_active">Active Template</label>
              </div>
            </div>
          </div>
        </div>

        <hr>

        <h4>Template Fields</h4>
        <div id="fields-container">
          <?php $__currentLoopData = old('fields', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('compliance.templates.partials.field-row', [
          'index' => $index,
          'field' => (object)$field,
          'fieldTypes' => $fieldTypes,
          'errors' => $errors
          ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <button type="button" id="add-field" class="btn btn-sm btn-secondary mt-2">
          <i class="fas fa-plus"></i> Add Field
        </button>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Template
          </button>
          <a href="<?php echo e(route('compliance.templates.index')); ?>" class="btn btn-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.FIELD_ROW_URL = "<?php echo e(route('compliance.templates.field-row')); ?>";
  const initialFields = <?php echo json_encode(old('fields', []), 512) ?>;
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('components.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/compliance/templates/create.blade.php ENDPATH**/ ?>