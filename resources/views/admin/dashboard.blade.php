<!-- Admin Main Dashboard -->
@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid bg-white p-4 border rounded-4 shadow-sm">
    <div class="mb-4">
        <h4 class="fw-bold">👋 Welcome back, {{ auth()->user()->admin->first_name }}!</h4>
        <p class="text-muted">Here's a quick summary of your administrative activities.</p>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body d-flex flex-column align-items-start">
                    <h6 class="text-muted">📚 Total Students</h6>
                    <h3 class="fw-bold">{{ $studentCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body d-flex flex-column align-items-start">
                    <h6 class="text-muted">👨‍🏫 Total Teachers</h6>
                    <h3 class="fw-bold">{{ $teacherCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body d-flex flex-column align-items-start">
                    <h6 class="text-muted">🗂️ Pending Requests</h6>
                    <h3 class="fw-bold">{{ $pendingDocRequests }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Requests Section -->
    <div class="mb-4">
        <h5 class="fw-bold mb-3">📄 Latest Document Requests</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>👤 Student</th>
                        <th>📑 Document</th>
                        <th>📂 Status</th>
                        <th>🕓 Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestDocRequests as $req)
                    <tr>
                        <td>{{ $req->student->first_name }} {{ $req->student->last_name }}</td>
                        <td>{{ $req->document_type }}</td>
                        <td><span class="badge bg-{{ $req->status === 'approved' ? 'success' : ($req->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($req->status) }}</span></td>
                        <td>{{ $req->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
