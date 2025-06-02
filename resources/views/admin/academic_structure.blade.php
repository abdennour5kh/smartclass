@extends('admin.layouts.admin')
@section('title', 'Academic Structure')

@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">
    <div id="toast-container"></div>
    <!-- Breadcrumb Tree -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light p-2 rounded">
            <li class="breadcrumb-item"><strong>Promotion:</strong> —</li>
            <li class="breadcrumb-item"><strong>Semester:</strong> —</li>
            <li class="breadcrumb-item"><strong>Section:</strong> —</li>
            <li class="breadcrumb-item"><strong>Group:</strong> —</li>
        </ol>
    </nav>

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

    <!-- Group Change Toggle Banner -->
    <div class="alert alert-primary d-flex justify-content-between align-items-center shadow-sm border border-primary-subtle px-4 py-3 rounded mb-4">
        <div>
            <h6 class="mb-1 fw-bold text-primary">🔁 Group Change Requests</h6>
            <p class="mb-0 text-muted small">Toggle this option to allow or disallow students to request group changes.</p>
        </div>

        <div class="form-check form-switch custom-switch d-flex align-items-center">
            @csrf
            <input class="form-check-input" type="checkbox" role="switch" id="groupChangeToggle"
                {{ $department->is_group_change_allowed ? 'checked' : '' }} >
        </div>

    </div>


    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3 nav-pills" id="structureTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab active" data-tabname="promotions" id="promotions-tab" data-bs-toggle="tab" data-bs-target="#promotions" type="button" role="tab">
                📚 Promotions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="semesters" id="semesters-tab" data-bs-toggle="tab" data-bs-target="#semesters" type="button" role="tab">
                🧭 Semesters
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="sections" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button" role="tab">
                🧩 Sections
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="groups" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button" role="tab">
                👥 Groups
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="modules" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button" role="tab">
                🧠 Modules
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="classes" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab">
                👩‍🏫 Classes
            </button>
        </li>


    </ul>

    <!-- Tab Contents -->
    <div class="tab-content bg-white p-4 rounded shadow-sm" id="structureTabsContent">
        <div class="tab-pane fade show active" id="promotions" role="tabpanel">
            <!-- Promotions Tab Content -->
            @include('admin.partials.promotions')
        </div>
        <div class="tab-pane fade" id="semesters" role="tabpanel">
            <!-- Semesters Tab Content -->
            @include('admin.partials.semesters')
        </div>
        <div class="tab-pane fade" id="sections" role="tabpanel">
            <!-- Sections Tab Content -->
            @include('admin.partials.sections')
        </div>
        <div class="tab-pane fade" id="groups" role="tabpanel">
            <!-- Groups Tab Content -->
            @include('admin.partials.groups')
        </div>
        <div class="tab-pane fade" id="modules" role="tabpanel">
            <!-- Modules Tab Content -->
            @include('admin.partials.modules')
        </div>
        <div class="tab-pane fade" id="classes" role="tabpanel">
            <!-- Classes Tab Content -->
            @include('admin.partials.classes')
        </div>
    </div>
</div>
@endsection
