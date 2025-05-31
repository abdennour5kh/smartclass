<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Change Password to Continue</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Quicksand', sans-serif;
      background: url("{{ asset('images/bg.png') }}") no-repeat center center fixed;
      background-size: cover;
    }

    .page-body-wrapper,
    .content-wrapper,
    .auth {
      background: transparent !important;
    }

    .auth-form-light {
      background-color: rgba(255, 255, 255, 0.94);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 1;
    }

    .brand-logo img {
      max-width: 140px;
    }

    .welcome-title {
      font-size: 22px;
      font-weight: 600;
      text-align: center;
      margin-bottom: 10px;
      color: #333;
    }

    .welcome-message {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .btn-success {
      background-color: #4ebe38;
      border-color: #4ebe38;
    }

    .btn-success:hover {
      background-color: #43aa31;
      border-color: #43aa31;
    }

    .text-center-button {
      display: flex;
      justify-content: center;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 38px;
      font-size: 18px;
      cursor: pointer;
      color: #888;
      z-index: 10;
    }

    .toggle-password:hover {
      color: #4ebe38;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0" style="min-height: 100vh;">
        <div class="row w-100 mx-0">
          <div class="col-lg-6 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo text-center mb-4">
                <img src="{{ asset('images/lock.png') }}" alt="SmartClass logo">
              </div>

              <div class="welcome-title">👋 Welcome to SmartClass</div>

              <p class="welcome-message">
                It's your first time logging in!<br>
                At SmartClass, your information security is our top priority.<br>
                Please update your password now to unlock all features of the platform.
              </p>

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form method="POST" action="{{ route('global_update_password') }}">
                @csrf

                <div class="form-group mb-3 position-relative">
                  <label for="current_password">🔑 Current Password</label>
                  <input type="password" class="form-control password-field" name="current_password" id="current_password" required>
                  <span class="toggle-password mdi mdi-eye-off" data-target="current_password"></span>
                </div>

                <div class="form-group mb-3 position-relative">
                  <label for="new_password">🆕 New Password</label>
                  <input type="password" class="form-control password-field" name="new_password" id="new_password" required>
                  <span class="toggle-password mdi mdi-eye-off" data-target="new_password"></span>
                </div>

                <div class="form-group mb-4 position-relative">
                  <label for="new_password_confirmation">✅ Confirm New Password</label>
                  <input type="password" class="form-control password-field" name="new_password_confirmation" id="new_password_confirmation" required>
                  <span class="toggle-password mdi mdi-eye-off" data-target="new_password_confirmation"></span>
                </div>

                <div class="text-center-button">
                  <button type="submit" class="btn btn-success px-4">🔁 Update Password</button>
                </div>
              </form>
            </div>
            <p class="text-white text-center mt-3 small">SmartClass © {{ date('Y') }} – All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('.toggle-password').forEach(function (icon) {
      icon.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);

        if (input.type === 'password') {
          input.type = 'text';
          this.classList.remove('mdi-eye-off');
          this.classList.add('mdi-eye');
        } else {
          input.type = 'password';
          this.classList.remove('mdi-eye');
          this.classList.add('mdi-eye-off');
        }
      });
    });
  </script>
</body>
</html>
