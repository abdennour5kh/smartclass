@extends('teacher.layouts.teacher')

@section('title', 'Attendance Sheet')
@section('content')

<script>
    $(document).ready(function() {
        $('body').toggleClass('sidebar-icon-only');
    });
</script>

<div class="container-fluid bg-white p-4 border">
    <div id="toast-container"></div>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
        <div class="page-title">
            Attendance for Session #{{ $session->id }}
        </div>
        <div class="d-flex flex-wrap">
            <form action="{{ route('teacher_export_attendance') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="sessions[]" value="{{ $session->id }}">
                <input type="hidden" name="group_id" value="{{ $session->classe->group_id }}">

                <button type="submit" class="btn btn-sm btn-primary ml-2 mb-3">⬇️ Export Excel Sheet</button>
            </form>

        </div>
    </div>
    <p>— {{ \Carbon\Carbon::parse($session->date)->toDateString() }}</p>
    @if (in_array($session->status, ['completed', 'canceled']))
    <div class="alert alert-warning">
        🛑 This session is <strong>{{ ucfirst($session->status) }}</strong>. Attendance cannot be modified.
    </div>
    @endif


    <div class="table-responsive">
        <table class="w-100 table table-bordered table-hover table-striped" id="smartClassTable">
            <thead class="table-dark">
                <tr>
                    <th data-priority="1">Registration Number</th>
                    <th data-priority="2">👤 Last Name</th>
                    <th data-priority="3">👥 First Name</th>
                    <th data-priority="4">📝 Note</th>
                    <th data-priority="5">📊 Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($session->classe->group->students as $student)
                @php
                $att = $session->attendances->firstWhere('student_id', $student->id);
                @endphp
                <tr>
                    <td>{{ $student->registration_num }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>
                        <textarea
                            class="form-control notes-input"
                            data-student-id="{{ $student->id }}"
                            rows="1">{{ old("attendance.{$student->id}.notes", $att->notes ?? '') }}</textarea>
                    </td>
                    <td>
                        <select
                            class="form-control status-select"
                            data-student-id="{{ $student->id }}">
                            <option value="">Select Status</option>
                            <option value="present" {{ ($att->status ?? '') === 'present'   ? 'selected' : '' }}>✅ Present</option>
                            <option value="absent" {{ ($att->status ?? '') === 'absent'    ? 'selected' : '' }}>❌ Absent</option>
                            <option value="late" {{ ($att->status ?? '') === 'late'      ? 'selected' : '' }}>🕒 Late</option>
                            <option value="excused" {{ ($att->status ?? '') === 'excused'   ? 'selected' : '' }}>🏷️ Excused</option>
                            <option value="justified" {{ ($att->status ?? '') === 'justified' ? 'selected' : '' }}>👍 Justified</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<script>
    const url = "{{route('teacher_store_attendance', $session)}}";
    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const attendanceUrl = url;
        const isSessionLocked = @json(in_array($session->status, ['completed', 'canceled']));

        // 2) CSRF for all AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // 3) Helper to save one record
        function saveAttendance(studentId, payload) {
            payload.student_id = studentId;
            payload._token = csrfToken;
            const data = {
                attendance: {}
            };
            data.attendance[studentId] = payload;
            return $.post(attendanceUrl, data);
        }

        function showToast(type, message, delay = 3000) {
            // build the toast HTML
            const toastId = `toast-${Date.now()}`;
            const $toast = $(`
                <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                    ${message}
                    </div>
                </div>
                </div>
            `);

            // append to container
            $('#toast-container').append($toast);

            // initialize and show
            const toast = new bootstrap.Toast(document.getElementById(toastId), {
                delay
            });
            toast.show();

            // remove from DOM when hidden
            document.getElementById(toastId)
                .addEventListener('hidden.bs.toast', () => $toast.remove());
        }



        // 4) Live-save listeners
        $('#smartClassTable')
            .on('change', '.status-select', function() {
                if (isSessionLocked) return;
                const sid = $(this).data('student-id'),
                    status = $(this).val();

                $(this).closest('td')
                    .removeClass('cell-present cell-absent cell-late cell-excused cell-justified')
                    .addClass('cell-' + status);

                saveAttendance(sid, {
                        status: status
                    })
                    .done(() => showToast('success', 'Status saved!'))
                    .fail(() => showToast('danger', 'Could not save status'));
            })
            .on('focusout', '.notes-input', function() {
                if (isSessionLocked) return;

                const sid = $(this).data('student-id'),
                    notes = $(this).val();

                saveAttendance(sid, {
                        notes: notes
                    })
                    .done(() => showToast('success', 'Note saved!'))
                    .fail(() => showToast('danger', 'Could not save note'));
            });
    });
</script>
@endsection