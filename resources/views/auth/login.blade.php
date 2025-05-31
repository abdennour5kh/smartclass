<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | SmartClass</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">

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

    .auth-form-btn {
      background-color: #4ebe38;
      border-color: #4ebe38;
    }

    .auth-form-btn:hover {
      background-color: #43aa31;
      border-color: #43aa31;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 12px;
      font-size: 20px;
      cursor: pointer;
      color: #888;
      z-index: 10;
    }

    .toggle-password:hover {
      color: #4ebe38;
    }

    select.form-control {
      font-size: 15px;
    }

    .text-white-muted {
      color: rgba(255, 255, 255, 0.8);
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0" style="min-height: 100vh;">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="SmartClass logo">
              </div>
              <h6 class="font-weight-light text-center mb-4">Sign in to continue</h6>

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form class="pt-3" method="POST" action="{{ route('login_submit') }}">
                @csrf

                <div class="form-group">
                  <label for="role">You are:</label>
                  <select name="role" class="form-control" id="role" onchange="toggleFields()" required>
                    <option value="s">Student</option>
                    <option value="t">Teacher</option>
                    <option value="a">Admin</option>
                  </select>
                </div>

                <div class="form-group" id="regNum">
                  <input type="text" name="registration_num" class="form-control form-control-lg" placeholder="Registration Number">
                </div>

                <div class="form-group" id="emailAdr" style="display: none;">
                  <input type="text" name="email_adr" class="form-control form-control-lg" placeholder="Email Address">
                </div>

                <div class="form-group" id="username" style="display: none;">
                  <input type="text" name="username" class="form-control form-control-lg" placeholder="Username">
                </div>

                <div class="form-group position-relative">
                  <input type="password" name="password" class="form-control form-control-lg" id="login_password" placeholder="Password" required>
                  <span class="toggle-password mdi mdi-eye-off" data-target="login_password"></span>
                </div>

                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-success btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                </div>

                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input" name="remember">
                      Keep me signed in
                      <i class="input-helper"></i>
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black">Forgot password?</a>
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
    function toggleFields() {
      const role = document.getElementById('role').value;
      document.getElementById('regNum').style.display = (role === 's') ? 'block' : 'none';
      document.getElementById('emailAdr').style.display = (role === 't') ? 'block' : 'none';
      document.getElementById('username').style.display = (role === 'a') ? 'block' : 'none';
    }

    toggleFields(); // run on load

    document.querySelectorAll('.toggle-password').forEach(icon => {
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
