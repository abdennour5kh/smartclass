@extends('student.layouts.student')
@section('title', 'Classe Details')
@section('content')

<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    📘 Attendance History - {{ $module->name }}
                </div>
                <div class="card-description">
                    All your sessions for this module, attendance status, and more.
                </div>
                <!-- Search and Export Row -->
                <div class="d-flex justify-content-between align-items-center my-3 flex-wrap gap-2">
                    <input type="text" id="smartClassTableSearch" class="form-control w-50" placeholder="🔍 Search by teacher, type...">
                </div>

                <!-- Attendance Table -->
                <div class="table-responsive mb-5">
                    <table id="smartClassTable" class="table table-bordered table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Teacher</th>
                                <th>Room</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendance as $entry)
                            <tr>
                                <td>{{$entry['date']}}</td>
                                <td>{{$entry['start']}} - {{$entry['end']}}</td>
                                <td>{{$entry['note']}}</td>
                                @if ($entry['status'] == 'present')
                                <td><span class="badge bg-success">Present</span></td>
                                @endif
                                @if ($entry['status'] == 'absent')
                                <td><span class="badge bg-warning">Absent</span></td>
                                @endif
                                @if ($entry['status'] == 'late')
                                <td><span class="badge bg-warning">Late</span></td>
                                @endif
                                @if ($entry['status'] == 'justified')
                                <td><span class="badge bg-warning">Justified</span></td>
                                @endif
                                @if ($entry['status'] == 'excused')
                                <td><span class="badge bg-success">Excused</span></td>
                                @endif
                                <td>{{$entry['teacher']}}</td>
                                <td>{{$entry['room']}}</td>
                                <td>{{$entry['type']}}</td>
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