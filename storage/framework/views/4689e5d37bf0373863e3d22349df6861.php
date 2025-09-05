<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ms-auto">
    <!-- Notifications Dropdown -->
    <li class="nav-item dropdown">
      <?php $unreadCount = auth()->user()->unreadNotifications->count(); ?>
      <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="far fa-bell"></i>
        <?php if($unreadCount > 0): ?>
        <span class="badge bg-warning navbar-badge"><?php echo e($unreadCount); ?></span>
        <?php endif; ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg" aria-labelledby="notificationDropdown">
        <li class="dropdown-item-text">
          You have <?php echo e($unreadCount); ?> notifications
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>

        <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li>
          <a href="<?php echo e($notification->data['url'] ?? '#'); ?>" class="dropdown-item" onclick="markNotificationAsRead('<?php echo e($notification->id); ?>')">
            <div class="d-flex flex-column">
              <div><i class="fas fa-file-alt me-2 text-warning"></i> <strong><?php echo e($notification->data['title'] ?? 'Notification'); ?></strong></div>
              <small><?php echo e($notification->data['message'] ?? ''); ?></small>
              <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo e($notification->created_at->diffForHumans()); ?></small>
            </div>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li class="dropdown-item-text">No notifications found</li>
        <?php endif; ?>

        <li><a href="#" class="dropdown-item dropdown-footer">See All Notifications</a></li>
      </ul>
    </li>

    <!-- User Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php if(Auth::user()->profile_picture): ?>
        <img src="<?php echo e(asset('storage/' . Auth::user()->profile_picture)); ?>" alt="Profile" class="img-circle me-2" width="32" height="32" style="object-fit: cover;">
        <?php else: ?>
        <div class="img-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
          <i class="fas fa-user"></i>
        </div>
        <?php endif; ?>
        <span class="ms-1"><?php echo e(Auth::user()->name); ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li>
          <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item">
            <i class="fas fa-user me-2"></i> Edit Profile
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="dropdown-item text-danger">
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/components/header.blade.php ENDPATH**/ ?>