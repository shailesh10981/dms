

<?php $__env->startSection('title', 'Documents'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Documents</h2>
    </div>
    <div class="col-md-6 text-right">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('document_upload')): ?>
      <a href="<?php echo e(route('documents.create')); ?>" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Document
      </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6">
          <form method="GET" action="<?php echo e(route('documents.index')); ?>" class="form-inline">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo e(request('search')); ?>">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-6">
          <form method="GET" action="<?php echo e(route('documents.index')); ?>" id="filter-form">
            <div class="row">
              <div class="col">
                <select name="department_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Departments</option>
                  <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($department->id); ?>" <?php echo e(request('department_id') == $department->id ? 'selected' : ''); ?>>
                    <?php echo e($department->name); ?>

                  </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col">
                <select name="location_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Locations</option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($location->id); ?>" <?php echo e(request('location_id') == $location->id ? 'selected' : ''); ?>>
                    <?php echo e($location->name); ?>

                  </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col">
                <select name="status" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Statuses</option>
                  <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                  <option value="submitted" <?php echo e(request('status') == 'submitted' ? 'selected' : ''); ?>>Submitted</option>
                  <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                  <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                </select>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Document ID</th>
              <th>Title</th>
              <th>Department</th>
              <th>Status</th>
              <th>Uploaded By</th>
              <th>Upload Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($document->document_id); ?></td>
              <td><?php echo e($document->title); ?></td>
              <td><?php echo e($document->department->name); ?></td>
              <td><?php echo $document->latestVersion->status_badge; ?></td>
              <td><?php echo e($document->uploader->name); ?></td>
              <td><?php echo e($document->created_at->format('M d, Y H:i')); ?></td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="<?php echo e(route('documents.show', $document->id)); ?>" class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('document_download')): ?>
                  <a href="<?php echo e(route('documents.download', $document->id)); ?>" class="btn btn-secondary" title="Download">
                    <i class="fas fa-download"></i>
                  </a>
                  <?php endif; ?>
                  <?php if($document->status == 'draft' && $document->uploaded_by == auth()->id()): ?>
                  <a href="<?php echo e(route('documents.edit', $document->id)); ?>" class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="<?php echo e(route('documents.destroy', $document->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                  <?php if($document->status == 'draft' && $document->uploaded_by == auth()->id()): ?>
                  <form action="<?php echo e(route('documents.submit', $document->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-warning" title="Submit for Approval">
                      <i class="fas fa-paper-plane"></i>
                    </button>
                  </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="text-center">No documents found.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        <?php echo e($documents->appends(request()->query())->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/documents/index.blade.php ENDPATH**/ ?>