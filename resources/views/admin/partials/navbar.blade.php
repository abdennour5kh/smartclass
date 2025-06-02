    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" style="position: fixed;">
            <div class="navbar-brand-wrapper d-flex justify-content-center">
        <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">  
          <a class="navbar-brand brand-logo" href="index.html"><img src="{{ asset('images/logo.png') }}" alt="logo"/></a>
          <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{ asset('images/logo.png') }}" alt="logo"/></a>
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-sort-variant"></span>
          </button>
        </div>  
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav navbar-nav-right">
          
          
          <li class="nav-item dropdown mr-4">
              <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center notification-dropdown"
                id="notificationDropdown" href="#" data-toggle="dropdown">
                  <i class="mdi mdi-bell mx-0"></i>
                  @if (auth()->user()->unreadNotifications->count() > 0)
                    <span class="count">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                  @endif
              </a>

              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="notificationDropdown">
                  <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>

                  @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                      @php
                          $type = class_basename($notification->type);
                          $icon = 'mdi-bell-outline';
                          $bg = 'bg-info';

                          if ($type === 'JustificationSubmitted') {
                              $icon = 'mdi-account-box';
                              $bg = 'bg-success';
                          } elseif ($type === 'SomeImportantNotification') {
                              $icon = 'mdi-alert-circle-outline';
                              $bg = 'bg-warning';
                          }
                      @endphp

                      <a class="dropdown-item" href="#">
                          <div class="item-thumbnail">
                              <div class="item-icon {{ $bg }}">
                                  <i class="mdi {{ $icon }} mx-0"></i>
                              </div>
                          </div>
                          <div class="item-content">
                              <h6 class="font-weight-normal">
                                  {{ $notification->data['message'] ?? 'New notification' }}
                              </h6>
                              <p class="font-weight-light small-text mb-0 text-muted">
                                  {{ $notification->created_at->diffForHumans() }}
                              </p>
                          </div>
                      </a>
                  @empty
                      <div class="dropdown-item text-center text-muted">
                          No new notifications
                      </div>
                  @endforelse

                  <div class="dropdown-divider"></div>

                  <form method="POST" action="{{ route('notifications.markAllRead') }}">
                      @csrf
                      <button type="submit" class="dropdown-item text-center text-primary small border-0 bg-transparent">
                          📬 Mark all as read
                      </button>
                  </form>
              </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="{{ $admin->user->avatar_url }}" alt="profile"/>
              <span class="nav-profile-name">{{ $admin->first_name }} {{ $admin->last_name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="{{ route('admin_dashboard_profile') }}">
                <i class="mdi mdi-settings text-primary"></i>
                Profile
              </a>
              <a class="dropdown-item" href="{{ route('logout') }}">
                <i class="mdi mdi-logout text-primary"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>