@extends('student.layouts.student')

@section('title', 'Document Requests')

@section('content')
<div class="container-fluid bg-white p-4 shadow-sm rounded">
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
<div class="alert alert-primary shadow-sm border border-primary-subtle px-4 py-3 rounded mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="flex-grow-1">
            <h6 class="mb-1 fw-bold text-primary">📄 Request a Document</h6>
            <p class="mb-0 text-muted small">Select the type of document you need and submit your request instantly.</p>
        </div>

        <form action="{{ route('student_store_document_request') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2" style="min-width: 360px;">
            @csrf
            <div class="form-group mb-0 mt-1" style="min-width: 200px;margin-right: 5px;">
                <select name="document_type" class="form-control" style="background: white;padding: 0.5rem 0.81rem !important;/*! border: 1px solid #4ebe38; *//*! margin-left: 5px; */color: #4d46468f !important;outline: 1px solid #4ebe38 !important;height: auto;" required>
                    @foreach ($documentTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0 mt-1">
                <button type="submit" class="btn btn-sm btn-primary">📨 Request</button>
            </div>
        </form>
    </div>
</div>


    {{-- Previous Requests Table --}}
    <hr class="my-4">
    <div class="page-title mb-3">📋 Previous Requests</div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped text-center" id="smartClassTable">
            <thead class="table-light">
                <tr>
                    <th>📄 Document Type</th>
                    <th>📅 Requested At</th>
                    <th>⏳ Status</th>
                    <th>🗨️ Admin Response</th>
                    <th>📥 Download</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documentRequests as $request)
                    <tr>
                        <td>{{ $request->document_type }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($request->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($request->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif ($request->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $request->admin_response ?? '—' }}</td>
                        <td>
                            @if ($request->status === 'approved' && $request->document_path)
                                <a href="{{ asset('storage/' . $request->document_path) }}" class="btn btn-sm btn-success" target="_blank">
                                    📥 Download
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Not Available</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No document requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
