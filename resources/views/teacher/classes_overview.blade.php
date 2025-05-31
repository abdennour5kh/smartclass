@extends('teacher.layouts.teacher')
@section('title', 'Classes Overview')
@section('content')
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}
.card-header {
    background: linear-gradient(to right, #007bff, #0056b3);
}
</style>
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">
    <div class="page-title">
    📚 Classes Overview
    </div>
    <p class="text-muted mb-5">Find & Manage your classes with ease.</p>

    @if($classes->isEmpty())
    <p>Sorry, there are no classes for the selected group. Please create a class first: 
        <strong><a href="{{ route('admin_academic_structure') }}">Create Class</a></strong>
    </p>
    @else
        <div class="row">
            @foreach($classes as $class)
                <div class="col-md-4 mb-4">
                    <div class="card classe-card shadow-sm rounded-4 h-100" style="cursor: pointer;" data-id="{{ $class->id }}">
                        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                🧠 {{ $class->module->name }} – Group {{ $class->group->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><strong>📚 Module:</strong> {{ $class->module->name }}</li>
                                <li><strong>👥 Group:</strong> {{ $class->group->name }}</li>
                                <li><strong>🏛️ Section:</strong> {{ $class->group->section->name }}</li>
                                <li><strong>🎓 Promotion:</strong> {{ $class->group->section->semester->promotion->name }}</li>
                                <li><strong>🧪 Type:</strong> {{ $class->class_type }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
<script>
    $('.classe-card').on('click', function() {
        classe_id = $(this).data('id');
        window.location.href = '/teacher/manage_classe/' + classe_id;
    });
</script>
@endsection