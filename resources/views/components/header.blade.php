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
      @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
      <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="far fa-bell"></i>
        @if ($unreadCount > 0)
        <span class="badge bg-warning navbar-badge">{{ $unreadCount }}</span>
        @endif
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg" aria-labelledby="notificationDropdown">
        <li class="dropdown-item-text">
          You have {{ $unreadCount }} notifications
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>

        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
        <li>
          <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item" onclick="markNotificationAsRead('{{ $notification->id }}')">
            <div class="d-flex flex-column">
              <div><i class="fas fa-file-alt me-2 text-warning"></i> <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong></div>
              <small>{{ $notification->data['message'] ?? '' }}</small>
              <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
            </div>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        @empty
        <li class="dropdown-item-text">No notifications found</li>
        @endforelse

        <li><a href="#" class="dropdown-item dropdown-footer">See All Notifications</a></li>
      </ul>
    </li>

    <!-- User Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        @if(Auth::user()->profile_picture)
        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="img-circle me-2" width="32" height="32" style="object-fit: cover;">
        @else
        <div class="img-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
          <i class="fas fa-user"></i>
        </div>
        @endif
        <span class="ms-1">{{ Auth::user()->name }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li>
          <a href="{{ route('profile.edit') }}" class="dropdown-item">
            <i class="fas fa-user me-2"></i> Edit Profile
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</nav>