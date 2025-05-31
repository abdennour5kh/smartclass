<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartClass</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- App Css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <div class="d-flex align-items-center vh-100">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 col-md-6 col-sm-8 mx-auto">
                    <div class="bg-white p-5 shadow-sm">
                        <img src="{{ asset('images/logo.png') }}" alt="logo" class="img-fluid mb-5 d-block mx-auto" style="width: 150px;">
                        <h6 class="font-weight-light">Sign in to Continue</h6>
                        <!-- Login Form -->
                        <form class="auth-form" method="POST" action="/">
                            @csrf
                            <!-- Username field -->
                            <div class="form-group mb-3">
                                
                                <input type="text" id="username" name="username" class="form-control form-control-lg" placeholder="Registration number" required>
                            </div>
                            
                            <!-- Password field -->
                            <div class="form-group mb-3">
                               
                                <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="*********" required>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-block mt-4">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>