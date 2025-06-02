@extends('admin.layouts.admin')
@section('title', 'Manage Students')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    🎓 Manage Students
                </div>
                <div class="card-description mb-4">
                    Here you can add, view, edit, or remove student records.
                </div>

                <!-- Search bar -->
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <input type="text" id="smartClassTableSearch" class="form-control" placeholder="🔍 Search by name, email, matricule...">
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-wrap justify-content-md-end">
                            <a href="{{ route('admin_create_student') }}" class="btn btn-success btn-sm mr-2 mb-2">➕ Add Student</a>
                            <button type="button" class="btn btn-secondary btn-sm mr-2 mb-2" data-bs-toggle="modal" data-bs-target="#importModal">📥 Import Excel</button>
                            <button id="exportButton" class="btn btn-outline-primary btn-sm mr-2 mb-2">⬇️ Export List</button>
                        </div>
                    </div>
                </div>

                <p class="text-center text-muted">
                    <strong>Export List</strong> button will only export the data currently visible based on the search or filters you applied.
                </p>

                <!-- Students Table -->
                <div class="table-responsive">
                    <table id="smartClassTable" class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th data-priority="1">👤 Name</th>
                                <th data-priority="2">🎓 Matricule</th>
                                <th data-priority="3">🏫 Promotion</th>
                                <th data-priority="4">👥 Group</th>
                                <th data-priority="5">🎂 Birth Date</th>
                                <th data-priority="6">🚻 Gender</th>
                                <th data-priority="7">⚙️ Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->registration_num }}</td>
                                <td>{{ $student->group->section->semester->promotion->name}}</td>
                                <td>{{ $student->group->name }}</td>
                                <td>{{ $student->birth_date}}</td>
                                <td>{{ $student->gender}}</td>
                                <td>
                                    <a href="{{ route('admin_edit_student', $student->id) }}" class="btn btn-sm btn-warning">📝</a>
                                    <form action="{{ route('admin_delete_student', $student->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" type="submit">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                            @if(empty($students))
                            <tr>
                                <td colspan="7" class="text-center">No students found 💤</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="importForm" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">📥 Import Students from Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
        </div>

        <div class="modal-body">
          <p class="mb-3">⚠️ Only Excel files (.xlsx, .xls) are accepted. Please follow the sample format.</p>
          <a href="{{ asset('samples/import_student_sample.xlsx') }}">import_student_sample.xlsx</a>
          <input class="form-control mb-3" type="file" name="excel_file" accept=".xlsx,.xls" required>

          <!-- Spinner -->
          <div id="uploadSpinner" class="align-items-center gap-2 text-primary d-none" >
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span>Uploading and importing... Please wait</span>
          </div>

          <!-- Success Message -->
          <div id="uploadSuccess" class="alert alert-success mt-3 d-none" role="alert">
            ✅ Students imported successfully!
          </div>

          <!-- Error Message -->
          <div id="uploadError" class="alert alert-danger mt-3 d-none" role="alert">
            ❌ Something went wrong. Please make sure to follow the right structure and try again.
          </div>

          <!-- Warning Message -->
        <div id="uploadWarning" class="alert alert-warning mt-3 d-none" role="alert">
          ⚠️ The import was partially successful: some rows were imported, while others were skipped due to data-related issues. A detailed report will be downloaded on your device automatically.
        </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  const importUrl = "{{ route('admin_import_student') }}";
  const exportUrl = "{{ route('admin_export_students') }}";
</script>

@endsection
