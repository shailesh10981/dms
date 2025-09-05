<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <title><?php echo e(config('app.name', 'Laravel')); ?> - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- AdminLTE CSS (for sidebar functionality) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php echo $__env->make('components.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <?php echo $__env->yieldContent('breadcrumbs'); ?>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="content">
        <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="container-fluid">
          <?php echo $__env->yieldContent('content'); ?>
        </div>
      </div>
    </div>

    <?php echo $__env->make('components.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE JS (for sidebar functionality) -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <?php echo $__env->yieldPushContent('scripts'); ?>


  <script>
    $(document).ready(function() {
      $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
      });
    });

    function markNotificationAsRead(notificationId) {
      fetch("<?php echo e(route('notifications.markAsRead')); ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
          },
          body: JSON.stringify({
            notification_id: notificationId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const badge = document.querySelector('.notifications-menu .navbar-badge');
            if (badge) {
              let count = parseInt(badge.textContent);
              count = Math.max(count - 1, 0);
              badge.textContent = count;
              if (count === 0) badge.style.display = 'none';
            }
          }
        });
    }
  </script>



</body>

</html><?php /**PATH C:\xampp\htdocs\dms-ldap\resources\views/components/app.blade.php ENDPATH**/ ?>