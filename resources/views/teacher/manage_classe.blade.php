@extends('teacher.layouts.teacher')
@section('title', 'Manage Class')
@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
        <div class="page-title mb-3">
            🧠 Module: Web Development
        </div>
        <div class="d-flex flex-wrap">
            <a href="{{ route('teacher_classes_overview') }}" class="btn btn-sm btn-outline-secondary ml-2 mb-3">Back to My Classes</a>
            <a href="{{ route('teacher_create_task') }}" class="btn btn-sm btn-secondary ml-2 mb-3">📝 Post Task</a>
            <a href="{{ route('teacher_announcement', $classe_id) }}" class="btn btn-sm btn-info text-white ml-2 mb-3">📢 Send Announcement</a>
            <form action="{{ route('teacher_export_attendance') }}" method="POST" class="d-inline">
                @csrf
                @foreach ($sessions as $session)
                    <input type="hidden" name="sessions[]" value="{{ $session->id }}">
                @endforeach
                <input type="hidden" name="group_id" value="{{ $session->classe->group_id }}">

                <button type="submit" class="btn btn-sm btn-primary ml-2 mb-3">⬇️ Export Entire Attendance</button>
            </form>
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
    @if (
    Carbon::parse($session->session_date)->toDateString() === $today
    && $session->status != 'completed'
    && $session->status != 'canceled'
    )
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
                    {{ $session->status }}
                </p>
                <p class="mb-1"><strong>Session Notes:</strong> {{ $currentSession->notes }}</p>
            </div>
            <div class="d-grid gap-2">
                <a href="{{ route('teacher_attendance_sheet', $currentSession->id) }}" class="btn btn-success">📋 Attendance Sheet</a>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">✏️ Add Note</button>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#endSessionModal">🛑 End Session</button>
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
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 table-hover align-middle" id="smartClassTable">
                            <thead class="table-light">
                                <tr>
                                    <th>📅 Date</th>
                                    <th>⏰ Time</th>
                                    <th>🏷️ Type</th>
                                    <th>📍 Location</th>
                                    <th>📓 Notes</th>
                                    <th>✅ Status</th>
                                    <th>⚙️ Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (
                                $sessions->sortByDesc('session_date')->filter(function ($s) use ($today) {
                                return
                                \Carbon\Carbon::parse($s->session_date)->toDateString() !== $today
                                || $s->status !== 'scheduled';
                                }) as $session) <tr>
                                    <td>{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
                                    <td>{{ $session->type }}</td>
                                    <td>{{ $session->location }}</td>
                                    <td>{{ $session->notes ?? '—' }}</td>
                                    <td>
                                        <span class="badge 
                                @if ($session->status === 'completed') bg-success 
                                @elseif ($session->status === 'canceled') bg-danger 
                                @elseif ($session->status === 'rescheduled') bg-warning text-dark
                                @else bg-secondary 
                                @endif">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher_attendance_sheet', $session->id) }}" class="btn btn-sm btn-outline-primary">📋 View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No past sessions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @if ($currentSession != null)
    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title" id="addNoteModalLabel">✏️ Add Note to Session</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="noteForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="session_id" value="{{ $currentSession->id }}">
                        <div class="form-group">
                            <label for="note">📝 Note</label>
                            <textarea name="note" id="note" class="form-control" rows="4" required>{{ $currentSession->notes }}</textarea>
                            <small class="text-danger d-none" id="note-error"></small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">💾 Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Session Modal -->
    <div class="modal fade" id="endSessionModal" tabindex="-1" aria-labelledby="endSessionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title" id="endSessionModalLabel">🛑 End Session Confirmation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to mark this session as <strong>completed</strong>?</p>
                    <p class="text-danger mb-0">⚠️ Make sure you’ve recorded attendance before ending the session.</p>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmEndSession" class="btn btn-danger">✅ Yes, End Session</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#noteForm').on('submit', function(e) {
                e.preventDefault();

                let sessionId = $('input[name="session_id"]').val();
                let note = $('#note').val();
                let $noteError = $('#note-error');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('teacher_update_session_note') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        session_id: sessionId,
                        note: note
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            $noteError.text('Unexpected error.').removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.note) {
                                $noteError.text(errors.note[0]).removeClass('d-none');
                            }
                        } else {
                            alert('Server error. Please try again.');
                        }
                    }
                });
            });
            $('#confirmEndSession').on('click', function() {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('teacher_end_session') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        session_id: "{{ $currentSession->id }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to end session.');
                        }
                    },
                    error: function() {
                        alert('Server error. Please try again.');
                    }
                });
            });
        });
    </script>

    @endif


    @endsection