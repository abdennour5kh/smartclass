@extends('student.layouts.student')

@section('title', '📘 Task Details')

@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 2rem; border: 1px solid #e7eaed;">
    <!-- Task Info -->
    <div class="page-title">📝 Task Information</div>
    <p class="text-muted">Review the task below and submit your work.</p>
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
    <div class="mb-4">
        <h5 class="mb-2">📌 <strong>{{ $task->title }}</strong></h5>
        <p class="mb-1">🧑‍🏫 Teacher: <strong>{{ $task->classe->teacher->user->full_name }}</strong></p>
        <p class="mb-1">⏳ Deadline: <strong>{{ \Carbon\Carbon::parse($task->deadline)->format('d/m/Y H:i') }}</strong></p>
        <p class="mt-3">📄 Description:</p>
        <div class="bg-light p-3 rounded border">{{ $task->description }}</div>
        @if ($task->teacherFiles->count())
        <hr>
        <div class="page-title mt-4">📚 Task Resources</div>
        <ul>
            @foreach ($task->teacherFiles as $file)
            @php
            $fullName = basename($file->file_path);
            $ext = pathinfo($fullName, PATHINFO_EXTENSION);
            $nameOnly = pathinfo($fullName, PATHINFO_FILENAME);
            $shortName = \Illuminate\Support\Str::limit($nameOnly, 25) . '.' . $ext;
            @endphp
            <li>
                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                    📄 {{ $shortName }}
                </a>
            </li>
            @endforeach
        </ul>
        @endif

    </div>

    <hr>

    @if (!$submission)
    <!-- Submission Form -->
    <div class="page-title mt-4">📤 Submit Your Task</div>
    <form action="{{ route('student_submit_task', $task) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label>💬 Message (optional)</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Write a message to your teacher...">{{ old('message') }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label>📎 Upload Your Work</label>
            <input type="file" name="files[]" class="form-control" multiple required>
            <small class="form-text text-muted">You can upload multiple files (PDF, DOCX, ZIP, etc.).</small>
        </div>

        <button type="submit" class="btn btn-success">✅ Submit Task</button>
    </form>
    @else
    <!-- Submission Details -->
    <div class="page-title mt-4">📥 Your Submission</div>

    <p>Status:
        @if ($submission->status == 'pending')
        ⏳ <span class="badge bg-warning text-dark">Pending</span>
        @elseif ($submission->status == 'approved')
        ✅ <span class="badge bg-success">Approved</span>
        @else
        ❌ <span class="badge bg-danger">Refused</span>
        @endif
    </p>

    <p>Grade: <strong>{{ $submission->grade ?? '—' }}</strong></p>
    <p>Feedback: <em>{{ $submission->feedback ?? 'No feedback yet' }}</em></p>

    @if ($submission->message)
    <p class="mt-3">📝 Your Message:</p>
    <div class="bg-light p-3 border rounded">{{ $submission->message }}</div>
    @endif

    @if ($submission->studentFiles->count())
    <p class="mt-4">📂 Submitted Files:</p>
    <ul>
        @foreach ($submission->studentFiles as $file)
        @php
        $fullName = basename($file->file_path);
        $ext = pathinfo($fullName, PATHINFO_EXTENSION);
        $nameOnly = pathinfo($fullName, PATHINFO_FILENAME);
        $shortName = \Illuminate\Support\Str::limit($nameOnly, 25) . '.' . $ext;
        @endphp
        <li>
            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                📄 {{ $shortName }}
            </a>
        </li>
        @endforeach
    </ul>
    @endif
    @endif
</div>
@endsection