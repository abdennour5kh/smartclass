@extends('student.layouts.student')

@section('title', 'Group Change Request')

@section('content')
<div class="container-fluid bg-white p-4 border rounded">
    <div class="page-title mb-4">
        🔄 Group Change Request
    </div>

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
                
    {{-- Existing Requests --}}
    @if ($requests->count())
        <div class="mb-5">
            <h5 class="mb-3">📜 Your Previous Requests</h5>
            <div class="list-group">
                @foreach($requests as $req)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>📅 Submitted:</strong> {{ $req->created_at->format('d M Y H:i') }}<br>
                                <strong>🎯 Requested Group:</strong> {{ $req->toGroup->name }}<br>
                                <strong>📝 Reason:</strong> {{ $req->reason }}
                            </div>
                            <div>
                                @php
                                    $status = $req->status;
                                    $label = '⏳ Pending';
                                    $badge = 'warning';

                                    if ($status === 'approved') {
                                        $label = '✅ Approved';
                                        $badge = 'success';
                                    } elseif ($status === 'rejected') {
                                        $label = '❌ Rejected';
                                        $badge = 'danger';
                                    }
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- New Request Form --}}
    <h5 class="mb-3 mt-5">🆕 Submit a New Request</h5>
    <form method="POST" action="{{ route('student_submit_group_change') }}">
        @csrf

        <div class="form-group mb-3">
            <label for="target_group_id" class="form-label">👥 Choose Target Group</label>
            <select name="target_group_id" id="target_group_id" class="form-control" required>
                <option disabled selected>-- Select Group --</option>
                @foreach($availableGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-4">
            <label for="reason" class="form-label">✍️ Reason for Request</label>
            <textarea name="reason" id="reason" rows="4" class="form-control" required></textarea>
        </div>

        <div class="form-group text-end">
            <button type="submit" class="btn btn-primary">📤 Submit Request</button>
        </div>
    </form>
</div>
@endsection
