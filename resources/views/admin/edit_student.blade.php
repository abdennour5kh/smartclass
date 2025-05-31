@extends('admin.layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">🛠️ Edit Student Profile</h4>
                <p class="card-description mb-3">
                    The student will be assigned to the selected Promotion, Semester, Section, and Group.
                </p>

                <!-- Info Alert -->
                <div class="alert alert-info">
                    ℹ️ If the group you want doesn't exist, please create it first from the <strong>Groups Management</strong> page.
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        ⚠️ Please fix the following issues:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add Student Form -->
                <form method="POST" action="{{ route('admin_update_student', $student->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <!-- Promotion -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="promotion">🎓 Promotion</label>
                                <select name="promotion_id" id="promotion" class="form-control promotion-select" required>
                                    <option value="">{{ $student->group->section->semester->promotion->name }}</option>
                                    @foreach($promotions as $promotion)
                                        <option value="{{ $promotion->id }}">{{ $promotion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="semester">📚 Semester</label>
                                <select name="semester_id" id="semester" class="form-control semester-select" required>
                                    <option value="">{{ $student->group->section->semester->name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Section -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="section">🏛️ Section</label>
                                <select name="section_id" id="section" class="form-control section-select" required>
                                    <option value="">{{ $student->group->section->name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Group -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="group">👥 Group</label>
                                <select name="group_id" id="group" class="form-control group-select" required>
                                    <option value="">{{ $student->group->name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">🧍 First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ $student->first_name }}" required>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">🧍 Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ $student->last_name }}" required>
                            </div>
                        </div>

                        <!-- Registration Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="registration_num">🆔 Registration Number</label>
                                <input type="text" class="form-control" name="registration_num" value="{{ $student->registration_num }}" required>
                            </div>
                        </div>


                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">📧 Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $student->email }}" required>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone_number">📱 Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ $student->phone_number }}" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address">📍 Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ $student->address }}</textarea>
                            </div>
                        </div>

                        <!-- Birth Date -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="birth_date">🎂 Birth Date</label>
                                <input type="date" class="form-control" name="birth_date" value="{{ $student->birth_date }}" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="gender">⚧️ Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">{{ $student->gender ?? 'Select Gender' }}</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="col-md-12">
                            <div class="form-group mb-5">
                                <label for="avatar">🖼️ Profile Image</label>
                                @if ($student->img_url)
                                    <img src="{{ asset('storage/'.$student->img_url) }}" class="rounded-circle mb-2 mt-5" width="100" height="100" alt="Student Profile Photo">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle mb-2 mt-5" width="100" height="100" alt="">
                                @endif
                                <input class="form-control" type="file" name="avatar" accept="image/*">
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary mb-5">💾 Add Student</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

