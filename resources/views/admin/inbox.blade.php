@extends('admin.layouts.admin')
@section('title', 'Inbox')

@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">

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
    <div class="page-title">
        📥 Inbox
    </div>
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-3 nav-pills" id="structureTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab active" data-tabname="justifications" id="justifications-tab" data-bs-toggle="tab" data-bs-target="#justifications" type="button" role="tab">
                📜 Justifications
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ac-tab" data-tabname="group_changes" id="group-changes-tab" data-bs-toggle="tab" data-bs-target="#groupChanges" type="button" role="tab">
                🔄 Group Change Requests
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content bg-white p-4 rounded shadow-sm" id="structureTabsContent">
        <div class="tab-pane fade show active" id="justifications" role="tabpanel">
            <!-- Promotions Tab Content -->
            @include('admin.partials.justifications')
        </div>
        <div class="tab-pane fade" id="groupChanges" role="tabpanel">
            @include('admin.partials.group_changes')
        </div>
    </div>
</div>
@endsection