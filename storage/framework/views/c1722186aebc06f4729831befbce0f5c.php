

<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>User Management</h1>
    </div>
    <div class="col-sm-6 text-right">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_create')): ?>
      <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New User
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Roles</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($user->id ?? 'N/A'); ?></td>
            <td>
              <div class="d-flex align-items-center">
                <?php if($user->profile_picture): ?>
                <img src="<?php echo e(asset('storage/' . $user->profile_picture)); ?>"
                  alt="<?php echo e($user->name); ?>"
                  class="img-circle img-size-32 mr-2">
                <?php else: ?>
                <div class="img-circle img-size-32 bg-secondary d-flex align-items-center justify-content-center mr-2">
                  <i class="fas fa-user text-white"></i>
                </div>
                <?php endif; ?>
                <?php echo e($user->name); ?>

              </div>
            </td>
            <td><?php echo e($user->email); ?></td>
            <td><?php echo e($user->department->name ?? 'N/A'); ?></td>
            <td>
              <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="badge badge-primary"><?php echo e($role->name); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td>
              <span class="badge badge-success">Active</span>
            </td>
            <td>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_view')): ?>
              <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
              </a>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_edit')): ?>
              <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </a>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_delete')): ?>
              <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/users/index.blade.php ENDPATH**/ ?>