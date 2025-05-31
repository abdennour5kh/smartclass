@extends('student.layouts.student')
@section('title', 'Tasks')
@section('content')

<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-title">📘 Tasks - {{ $module->name ?? 'N/A' }}</div>
                <div class="card-description">View and submit your tasks before deadlines.</div>

                <div class="table-responsive mb-5">
                    <table class="table table-bordered table-hover table-striped" id="smartClassTable">
                        <thead class="table-dark">
                            <tr>
                                <th>📌 Title</th>
                                <th>📎 Attachment</th>
                                <th>⏰ Deadline</th>
                                <th>📅 Created</th>
                                <th>📤 Your Submission</th>
                                <th>⚙️ Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                @php
                                    $now = now();
                                    $deadline = \Carbon\Carbon::parse($task->deadline);
                                    $submission = $task->submissions->where('student_id', $student->id)->first();
                                @endphp
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($task->title, 20) }}</td>
                                    <td>
                                        @foreach ($task->teacherFiles as $file)
                                            @php
                                                $fullName = basename($file->file_path);
                                                $extension = pathinfo($fullName, PATHINFO_EXTENSION);
                                                $nameOnly = pathinfo($fullName, PATHINFO_FILENAME);
                                                $limitedName = \Illuminate\Support\Str::limit($nameOnly, 10);
                                            @endphp
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">📄 {{ $limitedName . '.' . $extension }}</a><br>
                                        @endforeach
                                    </td>
                                    <td>{{ $deadline->format('d M Y H:i') }}</td>
                                    <td>{{ $task->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if ($submission)
                                            <span class="badge bg-success">Submitted</span><br>
                                            <small>{{ $submission->created_at->diffForHumans() }}</small>
                                            @if ($submission->studentFiles->isNotEmpty())
                                                <div class="mt-2">
                                                    @foreach ($submission->studentFiles as $file)
                                                        @php
                                                            $fullName = basename($file->file_path);
                                                            $extension = pathinfo($fullName, PATHINFO_EXTENSION);
                                                            $nameOnly = pathinfo($fullName, PATHINFO_FILENAME);
                                                            $limitedName = \Illuminate\Support\Str::limit($nameOnly, 10);
                                                        @endphp
                                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">📎 {{ $limitedName . '.' . $extension }}</a><br>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not submitted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($now->lt($deadline))
                                            @if ($submission)
                                                <a href="{{ route('student_submit_task', $task) }}" class="btn btn-sm btn-info">👁️ View</a>
                                            @else
                                                <a href="{{ route('student_submit_task', $task) }}" class="btn btn-sm btn-primary">📤 View and Submit</a>
                                            @endif
                                        @else
                                            <span class="text-muted">⛔ Deadline Passed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
