@extends('admin.layouts.admin')

@section('title', 'Edit Teacher Profile')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-5">🛠️ Edit Professor Profile</h4>
                <div class="card-discreption">
                    @if (session('success'))
                        <div class="alert alert-success">
                            ✅ {{ session('success') }}
                        </div>
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
                </div>
                <form method="POST" action="{{ route('admin_update_teacher', $teacher->id) }}" enctype="multipart/form-data">
                    @csrf
                
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">🧍 First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">🧍 Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">📧 Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $teacher->email) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone_number">📱 Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $teacher->phone_number) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label for="grade">💼 Grade</label>
                                <!-- Grade -->
                                <select name="grade" id="grade_select" class="form-control" required>
                                    <option value="{{ old('', $teacher->grade) }}">{{ old('Select Grade', $teacher->grade) }}</option>
                                    <option value="Maître Assistant B">Maître Assistant B</option>
                                    <option value="Maître Assistant A">Maître Assistant A</option>
                                    <option value="Maître de Conférences A">Maître de Conférences A</option>
                                    <option value="Maître de Conférences B">Maître de Conférences B</option>
                                    <option value="Professeur">Professeur</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="department_id">🏫 Department</label>
                                <input type="text" class="form-control" name="department" value="{{ old('department', $teacher->department->department_name) }}" disabled>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address">📍 Address</label>
                                <textarea class="form-control" name="address" rows="3" required>{{ old('address', $teacher->address) }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            @if ($teacher->img_url)
                                <img src="{{ asset('storage/' . $teacher->img_url) }}" class="rounded-circle mb-2 mt-5" width="100" height="100" alt="Teacher Avatar">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" class="rounded-circle mb-2 mt-5" width="100" height="100" alt="Default Avatar">
                            @endif
                            <div class="form-group mb-5">
                                <label for="avatar" class="form-label">🖼️ Profile Avatar</label>
                                <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mb-5">💾 Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
