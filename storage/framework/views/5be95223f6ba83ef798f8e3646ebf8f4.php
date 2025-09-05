

<?php $__env->startSection('title', 'Role Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Role Management</h1>
    </div>
    <div class="col-sm-6 text-end">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role_create')): ?>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="fas fa-plus"></i> Add New Role
      </button>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Permissions</th>
          <th>Users</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($role->id); ?></td>
          <td><?php echo e($role->name); ?></td>
          <td>
            <?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge bg-info text-dark"><?php echo e($permission->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </td>
          <td><?php echo e($role->users_count ?? $role->users->count()); ?></td>
          <td>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role_edit')): ?>
            <button class="btn btn-sm btn-primary edit-role"
              data-id="<?php echo e($role->id); ?>"
              data-name="<?php echo e($role->name); ?>"
              data-permissions="<?php echo e(implode(',', $role->permissions->pluck('id')->toArray())); ?>">
              <i class="fas fa-edit"></i>
            </button>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role_delete')): ?>
            <?php if(!in_array($role->name, ['admin'])): ?>
            <form action="<?php echo e(route('admin.roles.destroy', $role->id)); ?>" method="POST" class="d-inline">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i>
              </button>
            </form>
            <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('admin.roles.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Role Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Permissions</label>
            <div class="permission-container" style="max-height: 300px; overflow-y: auto;">
              <?php
              $groupedPermissions = $permissions->groupBy('module');
              ?>
              <?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $modulePermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="card mb-2">
                <div class="card-header py-1">
                  <h6 class="mb-0"><?php echo e(ucfirst($module)); ?></h6>
                </div>
                <div class="card-body py-2">
                  <?php $__currentLoopData = $modulePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-check">
                    <input type="checkbox" name="permissions[]" id="perm_<?php echo e($permission->id); ?>"
                      value="<?php echo e($permission->id); ?>" class="form-check-input">
                    <label for="perm_<?php echo e($permission->id); ?>" class="form-check-label">
                      <?php echo e($permission->name); ?>

                    </label>
                  </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" id="editRoleForm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="edit_name">Role Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Permissions</label>
            <div class="permission-container" style="max-height: 300px; overflow-y: auto;">
              <?php $__currentLoopData = $permissions->groupBy('module'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $modulePermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="card mb-2">
                <div class="card-header py-1">
                  <h6 class="mb-0"><?php echo e(ucfirst($module)); ?></h6>
                </div>
                <div class="card-body py-2">
                  <?php $__currentLoopData = $modulePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-check">
                    <input type="checkbox" name="permissions[]" id="edit_perm_<?php echo e($permission->id); ?>"
                      value="<?php echo e($permission->id); ?>" class="form-check-input">
                    <label for="edit_perm_<?php echo e($permission->id); ?>" class="form-check-label">
                      <?php echo e($permission->name); ?>

                    </label>
                  </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Role</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  $(document).ready(function() {
    $('.edit-role').click(function() {
      const roleId = $(this).data('id');
      const roleName = $(this).data('name');
      const permissionsStr = $(this).data('permissions') || '';
      const rolePermissions = permissionsStr.toString().split(',').map(id => parseInt(id));

      $('#editRoleForm').attr('action', '/admin/roles/' + roleId);
      $('#edit_name').val(roleName);

      // Uncheck all
      $('#editRoleModal input[type="checkbox"]').prop('checked', false);

      // Check selected permissions
      rolePermissions.forEach(function(permissionId) {
        $('#edit_perm_' + permissionId).prop('checked', true);
      });

      const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
      modal.show();
    });

    $('#editRoleForm').submit(function(e) {
      e.preventDefault();
      const form = $(this);

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        success: function() {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editRoleModal'));
          modal.hide();
          location.reload();
        },
        error: function(xhr) {
          alert('Error: ' + xhr.responseText);
        }
      });
    });
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('components.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/roles/index.blade.php ENDPATH**/ ?>