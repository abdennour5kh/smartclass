@extends('teacher.layouts.teacher')
@section('title', 'Manage Class')
@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
        <div class="page-title mb-3">
            🧠 Module: Web Development
        </div>
        <div class="d-flex flex-wrap">
            <a href="#" class="btn btn-sm btn-outline-primary ml-2 mb-3">Back to My Classes</a>
            <button class="btn btn-sm btn-secondary ml-2 mb-3">📝 Post Task</button>
            <a href="{{ route('teacher_announcement', $classe_id) }}" class="btn btn-sm btn-info text-white ml-2 mb-3">📢 Send Announcement</a>
        </div>
    </div>
    <p class="mb-3">Group: A — Section: 1 — Promotion: Licence 3 Info</p>

    <!-- Current Session Section -->
     @php
         use Carbon\Carbon;
         $today = Carbon::today()->toDateString();
         $currentSession = null;
     @endphp
     @foreach ($sessions as $session)
        @if (Carbon::parse($session->session_date)->toDateString() === $today)
            @php $currentSession = $session; break; @endphp
        @endif
     @endforeach
    @if ($currentSession)
        @php
            $startTime = Carbon::parse($currentSession->start_time);
            $endTime = Carbon::parse($currentSession->end_time);
            $nowTime = Carbon::now();
            $statusLabel = '';

            if ($nowTime->lt($startTime)) {
                $statusLabel = 'Starts at: ' . $startTime->format('H:i');
            } elseif ($nowTime->between($startTime, $endTime)) {
                $statusLabel = 'Started at: ' . $startTime->format('H:i');
            } else {
                $statusLabel = 'Ended at: ' . $endTime->format('H:i');
            }
        @endphp
        <div class="card shadow-sm rounded-4 mb-4 border-warning border-2">
            <div class="card-header bg-warning text-dark rounded-top-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🔥 Session In Progress</h5>
                <span class="badge bg-white rounded-pill">{{ $statusLabel }}</span>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                <p class="mb-1"><strong>Date:</strong> {{ Carbon::parse($currentSession->session_date)->format('d M Y') }}</p>
                <p class="mb-1"><strong>Session Type:</strong> {{ $currentSession->type }}</p>
                <p class="mb-0"><strong>Status:</strong> 
                    @if ($nowTime->lt($startTime))
                        Upcoming
                    @elseif ($nowTime->between($startTime, $endTime))
                        Ongoing
                    @else
                        Finished
                    @endif
                </p>
                </div>
                <div class="d-grid gap-2">
                <a href="{{ route('teacher_attendance_sheet', $currentSession->id) }}" class="btn btn-success">📋 Start Attendance Sheet</a>
                <button class="btn btn-outline-primary">✏️ Add Note</button>
                <button class="btn btn-outline-danger">🛑 End Session</button>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info d-flex justify-content-between align-items-center rounded-4 shadow-sm">
            <div>
                <h5 class="mb-1">ℹ️ No Session in Progress</h5>
                <p class="mb-0">There is currently no active session for today.</p>
            </div>
        </div>
    @endif

    <div class="row">
    <!-- Left Column: Sessions Table -->
    <div class="col-lg-12 mb-4">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-header bg-primary text-white rounded-top-4">
          <h5 class="mb-0">📅 Sessions History</h5>
        </div>
        <div class="card-body p-0">
          
        </div>
      </div>
    </div>
</div>
@endsection