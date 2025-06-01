@extends('teacher.layouts.teacher')

@section('title', 'Tasks')

@section('content')
<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem; border: 1px solid #e7eaed;">
    <div class="page-title">📝 Create New Task</div>
    <p class="text-muted mb-4">Submit a task to a class. Attach documents and set deadlines.</p>

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


    <form action="{{ route('teacher_store_task') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>📘 Class</label>
                    @if ($classes)
                    <select name="classe_id" class="form-control" required>
                        <option value="">-- Select Class --</option>
                        @foreach ($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->group->name }} - {{ $classe->module->name }}</option>
                        @endforeach
                    </select>
                    @else
                    do it lates
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>📌 Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label>📝 Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>⏰ Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>📎 Attachment (optional)</label>
                    <input type="file" name="attachments[]" class="form-control" multiple>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">➕ Create Task</button>
    </form>

    <!-- Tasks Table Section -->
    <div class="page-title mt-5">📂 Your Tasks</div>
    <p class="text-muted mb-4">Browse tasks you created for your classes.</p>

    @if ($tasks->isNotEmpty())
    <div class="table-responsive">
        <table id="taskTable" class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>📌 Title</th>
                    <th>📘 Class</th>
                    <th>⏰ Deadline</th>
                    <th>📎 Attachment</th>
                    <th>📅 Created</th>
                    <th>🗂️ Submissions</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->classe->group->name ?? 'N/A' }} - {{ $task->classe->module->name ?? 'N/A' }}</td>
                    <td>{{ $task->deadline }}</td>
                    <td>
                        @if ($task->teacherFiles->isNotEmpty())
                        @foreach ($task->teacherFiles as $file)
                        <a class="btn btn-sm btn-success" href="{{ asset('storage/' . $file->file_path) }}" target="_blank">📄 Download</a><br>
                        @endforeach
                        @else
                        No file
                        @endif
                    </td>
                    <td>{{ $task->created_at->format('d M Y') }}</td>
                    <td>{{ $task->submissions->count() }}</td>
                    <td>
                        <div class="d-inline-flex gap-1">
                            <a class="btn btn-sm btn-danger text-white px-2 py-1 mr-2" href="{{ route('teacher_delete_task', $task) }}">🗑️</a>
                            @if ($task->submissions->count() > 0)
                            <a href="{{ route('teacher_task_submissions', $task) }}" class="btn btn-sm btn-warning px-2 py-1">👁️</a>
                            @endif
                        </div>
                    </td>


                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted">No tasks found.</p>
    @endif


</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let table = $('#taskTable').DataTable({
            "ordering": true,
            "pageLength": 10,
            "language": {
                "search": "",
                "searchPlaceholder": "🔍 Search tasks..."
            }
        });

        $('#taskSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endsection