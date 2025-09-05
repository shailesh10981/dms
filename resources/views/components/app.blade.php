<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- AdminLTE CSS (for sidebar functionality) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    @include('components.header')
    @include('components.sidebar')

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                @yield('breadcrumbs')
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="content">
        @include('components.alerts')
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>
    </div>

    @include('components.footer')
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE JS (for sidebar functionality) -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  @stack('scripts')


  <script>
    $(document).ready(function() {
      $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
      });
    });

    function markNotificationAsRead(notificationId) {
      fetch("{{ route('notifications.markAsRead') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
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

</html>