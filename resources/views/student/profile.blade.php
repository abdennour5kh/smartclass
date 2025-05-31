@extends('student.layouts.student')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-5">🛠️ Update Your Profile</h4>

                @if (session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5 class="mb-3">⚠️ Please fix the following issues:</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('student_update_profile') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">🧍 First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $student->first_name) }}">
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">🧍 Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $student->last_name) }}">
                            </div>
                        </div>

                        <!-- Registration Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="registration_num">🎓 Registration Number</label>
                                <input type="text" class="form-control" name="registration_num" value="{{ old('registration_num', $student->registration_num) }}" disabled>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">📧 Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $student->email) }}" required>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone_number">📱 Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="0{{ old('phone_number', $student->phone_number) }}">
                            </div>
                        </div>

                        <!-- Birth Date -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="birth_date">🎂 Birth Date</label>
                                <input type="date" class="form-control" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label for="address">📍 Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                            </div>

                            @if ($student->img_url)
                                <img src="{{ asset('storage/' . $student->img_url) }}" class="rounded-circle mb-2" width="100" height="100" alt="Student Avatar">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle mb-2" width="100" height="100" alt="Default Avatar">
                            @endif
                            <div class="form-group mb-5">
                                <label for="avatar" class="form-label">🖼️ Profile Avatar</label>
                                <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mb-5">💾 Save Changes</button>
                </form>

                <hr>
                <h4 class="card-title mb-5 mt-5">🔐 Change Password</h4>
                <form method="POST" action="{{ route('student_update_password') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="current_password">🔑 Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="new_password">🆕 New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="new_password_confirmation">✅ Confirm New Password</label>
                        <input type="password" class="form-control" name="new_password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-warning">🔁 Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
