@extends('student.layouts.student')

@section('title', 'My Teachers')

@section('content')
<div class="container-fluid bg-white p-4 shadow-sm rounded">
    <div class="page-title mb-3">👨‍🏫 My Teachers</div>
    <p class="text-muted mb-4">These are the teachers assigned to your current classes.</p>

    <div class="row">
        @forelse ($currentStudentTeachers as $teacher)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-start border-4 border-primary rounded" style="border-left-width: 5px !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <img src="{{ $teacher->img_url ? asset('storage/' . $teacher->img_url) : asset('images/default-avatar.png') }}"
                                     alt="Avatar" class="rounded-circle" width="50" height="50">
                            </div>
                            <div class="ml-3">
                                <h6 class="mb-0 fw-bold">{{ $teacher->first_name }} {{ $teacher->last_name }}</h6>
                                <small class="text-muted">{{ $teacher->email }}</small>
                                <div class="text-muted small">{{ $teacher->grade }}</div>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <h6 class="text-muted">Teaches:</h6>
                            <ul class="list-unstyled small mb-0 mt-1">
                                @foreach ($teacher->classes_info as $class)
                                    <li>📘 {{ $class['module'] }} — <span class="text-muted">{{ $class['class_type'] }}</span></li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('student_compose_message', $teacher->user->id) }}" class="btn btn-sm btn-outline-primary">📩 Message</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No teachers found for your current classes.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
