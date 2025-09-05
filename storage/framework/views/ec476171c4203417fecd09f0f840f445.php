

<?php
use App\Models\Document;
use App\Models\User;
?>

<?php $__env->startSection('title', 'Audit Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Audit Logs</h2>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <form method="GET" action="<?php echo e(route('audit-logs.index')); ?>" id="filter-form">
        <div class="row">
          <div class="col-md-3">
            <select name="document_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Documents</option>
              <?php $__currentLoopData = Document::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($doc->id); ?>" <?php echo e(request('document_id') == $doc->id ? 'selected' : ''); ?>>
                <?php echo e($doc->document_id); ?> - <?php echo e($doc->title); ?>

              </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="user_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Users</option>
              <?php $__currentLoopData = User::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                <?php echo e($user->name); ?>

              </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="action" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Actions</option>
              <?php $__currentLoopData = ['upload', 'update', 'submit', 'approve', 'reject', 'download', 'preview', 'delete', 'restore']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                <?php echo e(ucfirst($action)); ?>

              </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <div class="input-group input-group-sm">
              <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>" placeholder="From">
              <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>" placeholder="To">
              <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-filter"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Timestamp</th>
              <th>Document</th>
              <th>User</th>
              <th>Action</th>
              <th>Details</th>
              <th>IP Address</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($log->created_at->format('M d, Y H:i:s')); ?></td>
              <td>
                <?php if($log->document): ?>
                <a href="<?php echo e(route('documents.show', $log->document_id)); ?>">
                  <?php echo e($log->document->document_id); ?>

                </a>
                <?php else: ?>
                N/A
                <?php endif; ?>
              </td>
              <td><?php echo e($log->user->name ?? 'System'); ?></td>
              <td><?php echo e(ucfirst($log->action)); ?></td>
              <td><?php echo e($log->details); ?></td>
              <td><?php echo e($log->ip_address); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center">No audit logs found.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        <?php echo e($logs->appends(request()->query())->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/audit-logs/index.blade.php ENDPATH**/ ?>