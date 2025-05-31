@extends('student.layouts.student')
@section('title', 'My Classes')

@section('content')
<div class="row">
  <div class="col-md-12 stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="card-title"> My classes </div>
        <!-- Search Input -->
        <div class="mb-4">
          <input type="text" id="searchInput" class="form-control" placeholder="Search for module or teacher...">
        </div>
        @php
        $department = Auth::user()->student->group->section->semester->promotion->department;
        @endphp

        @if($department->is_group_change_allowed)
        <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm mb-4" style="background-color: #e9f8ee; border-left: 5px solid #28a745;">
          <div>
            <strong>👥 Group Change Period is Open!</strong>
            <p class="mb-0 small">You can submit a request to move to another group. Make sure to explain your reason clearly.</p>
          </div>
          <div>
            <a href="{{ route('student_change_group') }}" class="btn btn-sm btn-outline-success">✏️ Request Group Change</a>
          </div>
        </div>
        @endif

        <div class="row" id="classesContainer">
          @foreach ($info as $i)
          <div class="col-md-6 mb-4 class-card">
            <div class="card shadow-sm border-start border-4 border-primary" style="border-left-width: 5px !important;">
              <div class="card-body">
                <h5 class="card-title mb-1">📚 <strong>{{ $i['module']->name }}</strong></h5>
                <p class="text-muted mb-2">👩‍🏫 {{ $i['teacher'] }} — <em>{{ $i['grade'] }}</em></p>

                <div class="mb-2">
                  <span>🧪 <strong>Class Type:</strong> {{ $i['type'] ?? 'N/A' }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span>📈 <strong>Attendance:</strong> {{ $i['attendanceRate'] }}%</span>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#class{{ $i['id'] }}">🔎 Details</button>
                </div>

                <div class="progress mb-2" style="height: 6px;">
                  <div class="progress-bar 
                        {{ $i['attendanceRate'] >= 75 ? 'bg-success' : ($i['attendanceRate'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                    role="progressbar"
                    aria-valuenow="{{ $i['attendanceRate'] }}"
                    aria-valuemin="0"
                    style="width: 50%"
                    id="classesProgressBar"
                    aria-valuemax="100">
                  </div>
                </div>

                <div class="collapse" id="class{{ $i['id'] }}">
                  <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('student_class_details', $i['id']) }}" class="btn btn-sm btn-secondary ml-2">📄 History</a>
                    <a href="{{ route('student_announcements', $i['id']) }}" class="btn btn-sm btn-info ml-2">📢 Announcements</a>
                    <a href="{{ route('student_view_tasks', $i['id']) }}" class="btn btn-sm btn-danger ml-2">📝 Tasks</a>
                  </div>
                </div>

              </div>
            </div>
          </div>
          @endforeach

        </div>
      </div>
    </div>
  </div>
</div>
<!-- Search Functionality -->
<script>
  document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.class-card').forEach(card => {
      const content = card.innerText.toLowerCase();
      card.style.display = content.includes(query) ? 'block' : 'none';
    });
  });
</script>
@endsection