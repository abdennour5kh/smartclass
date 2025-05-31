@extends('admin.layouts.admin')

@section('title', 'Add New Professor')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">👨‍🏫 Add New Professor</h4>
                <p class="card-description mb-5">
                    The new Professor will be assigned to the current admin Department.
                </p>
                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
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

                <!-- Add Professor Form -->
                <form method="POST" action="{{ route('admin_store_teacher') }}" enctype="multipart/form-data">
                    @csrf
                
                    <div class="row">

                        <!-- First Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">🧍 First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">🧍 Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">📧 Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone_number">📱 Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label for="grade">💼 Grade</label>
                                <!-- Grade -->
                                <select name="grade" id="grade_select" class="form-control" required>
                                    <option value="">Select Grade</option>
                                    <option value="Maître Assistant B">Maître Assistant B</option>
                                    <option value="Maître Assistant A">Maître Assistant A</option>
                                    <option value="Maître de Conférences A">Maître de Conférences A</option>
                                    <option value="Maître de Conférences B">Maître de Conférences B</option>
                                    <option value="Professeur">Professeur</option>
                                </select>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address">📍 Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="col-md-12">
                            <div class="form-group mb-5">
                                <label for="avatar" class="form-label">🖼️ Profile Image</label>
                                <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            </div>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mb-5">💾 Add Professor</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
