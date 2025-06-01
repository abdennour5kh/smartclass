@extends('teacher.layouts.teacher')

@section('title', 'Task Submissions')

@section('content')
<div class="container-fluid bg-white p-4" style="border: 1px solid #e7eaed;">
    <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

    <div class="page-title mb-3">👁️ Task Submissions</div>

    <div class="mb-4">
        <h5>📌 {{ $task->title }}</h5>
        <p class="text-muted mb-1">📝 {{ $task->description }}</p>
        <p class="text-muted mb-0">⏰ Deadline: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') }}</p>
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
    @if($submissions->isEmpty())
    <div class="alert alert-info">ℹ️ No students have submitted this task yet.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped" id="smartClassTable">
            <thead class="table-dark">
                <tr>
                    <th>👤 Student</th>
                    <th>📅 Submitted At</th>
                    <th>📎 Attachment</th>
                    <th>📝 Grades</th>
                    <th>📝 FeedBacks</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submissions as $submission)
                <tr>
                    <td>{{ $submission->student->first_name }} {{ $submission->student->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</td>
                    <td>
                        @if ($submission->studentFiles->isNotEmpty())
                        @foreach ($submission->studentFiles as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary mb-1" target="_blank">📄 View</a>
                        @endforeach
                        @else
                        <span class="text-muted">No file</span>
                        @endif
                    </td>
                    <td>
                        <textarea
                            class="form-control submission-grade"
                            rows="1"
                            data-submission-id="{{ $submission->id }}">{{ $submission->grade }}</textarea>
                    </td>
                    <td>
                        <textarea
                            class="form-control submission-feedback"
                            rows="1"
                            data-submission-id="{{ $submission->id }}">{{ $submission->feedback }}</textarea>
                    </td>

                    <td>
                        <div class="d-inline-flex gap-1">
                            @if ($submission->status === 'pending')
                            <a href="{{ route('teacher_submission_refuse', $submission) }}" class="btn btn-sm btn-danger text-white px-2 py-1 mr-2">Refuse</a>
                            <a href="{{ route('teacher_submission_approve', $submission) }}" class="btn btn-sm btn-success text-white px-2 py-1 mr-2">Approve</a>
                            @elseif ($submission->status === 'approved' || $submission->status === 'refused')
                            {{ $submission->status }}
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<script>
    const updategradeUrl = "{{ route('teacher_grade_submission') }}";
    const updatefeedbackUrl = "{{ route('teacher_feedback_submission') }}";

    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        function showToast(type, message, delay = 3000) {
            const toastId = `toast-${Date.now()}`;
            const $toast = $(`
                <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                    </div>
                </div>
            `);
            $('#toast-container').append($toast);
            new bootstrap.Toast(document.getElementById(toastId), {
                delay
            }).show();
            document.getElementById(toastId).addEventListener('hidden.bs.toast', () => $toast.remove());
        }

        $('.submission-grade').on('focusout', function() {
            const submissionId = $(this).data('submission-id');
            const grade = $(this).val();

            $.post(updategradeUrl, {
                submission_id: submissionId,
                grade: grade
            }).done(() => {
                showToast('success', 'grade saved!');
            }).fail(() => {
                showToast('danger', 'Failed to save grade!');
            });
        });
        $('.submission-feedback').on('focusout', function() {
            const submissionId = $(this).data('submission-id');
            const feedback = $(this).val();

            $.post(updatefeedbackUrl, {
                submission_id: submissionId,
                feedback: feedback
            }).done(() => {
                showToast('success', 'feedback saved!');
            }).fail(() => {
                showToast('danger', 'Failed to save feedback!');
            });
        });
    });
</script>
@endsection