@extends('teacher.layouts.teacher')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid bg-white p-4 border rounded-4 shadow-sm">
    <div>
        <!-- Welcome -->
    <div class="mb-4">
        <h4 class="fw-bold">👋 Welcome back, {{ auth()->user()->teacher->first_name }}!</h4>
        <p class="text-muted">Here's a quick summary of your teaching activities.</p>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stats-badge  border-0 shadow-sm rounded p-3">
                <h6 class="mb-1 text-muted">📘 Classes</h6>
                <h4>{{ $classCount }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-badge  border-0 shadow-sm rounded p-3">
                <h6 class="mb-1 text-muted">📥 Submissions to Grade</h6>
                <h4>{{ $pendingSubmissions }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-badge  border-0 shadow-sm rounded p-3">
                <h6 class="mb-1 text-muted">📑 Pending Justifications</h6>
                <h4>{{ $pendingJustifications }}</h4>
            </div>
        </div>
    </div>

    <!-- Today Session -->
    @forelse ($todaySessions as $tds)
        <div class="card shadow-sm rounded-4 border-warning mt-2">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h6 class="mb-1">🔥 You have a session today</h6>
                <p class="mb-0">{{ $tds->start_time }} — {{ $tds->end_time }} | {{ $tds->classe->group->name }} — {{ $tds->classe->module->name }}</p>
            </div>
            <a href="{{ route('teacher_manage_classe', $tds->id) }}" class="btn btn-success btn-sm mt-2 mt-md-0">📋 Dive in</a>
        </div>
    </div>
    @empty
        
    @endforelse

    <!-- Quick Actions -->
    <div class="mt-4">
        <h6 class="text-muted mb-3">Quick Actions</h6>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('teacher_create_task') }}" class="btn btn-outline-primary btn-sm mr-2">📝 Post Task</a>
            <a href="{{ route('teacher_classes_overview') }}" class="btn btn-outline-secondary btn-sm mr-2">📘 My Classes</a>
            <a href="{{ route('teacher_announcement') }}" class="btn btn-outline-info btn-sm">📢 Announcements</a>
        </div>
    </div>
    </div>
<div class="mt-5">
            <h6 class="text-muted mb-3">📊 Monthly Attendance Overview</h6>
            <div style="position: relative; width: 100%; height: auto;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const colors = [
            '#4ebe38', '#007bff', '#ffc107', '#e83e8c', '#20c997', '#6610f2',
            '#fd7e14', '#6f42c1', '#17a2b8', '#dc3545', '#28a745', '#ff6f61',
            '#00b894', '#0984e3', '#e17055', '#b71540', '#8e44ad', '#2ecc71',
            '#f39c12', '#1abc9c', '#c0392b', '#6c5ce7', '#d63031'
        ];

        // Loop through each stats card
        document.querySelectorAll('.stats-badge').forEach(card => {
            const color = colors[Math.floor(Math.random() * colors.length)];
            const transparent = color + '45'; // softer transparency

            // Set background and text color for the card
            card.style.backgroundColor = transparent;
            card.style.color = color;
        });

        // Attendance Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Present', 'Absent', 'Late', 'Justified', 'Excused'],
                datasets: [{
                    label: 'Students',
                    data: [
                        {{ $attendanceStats['present'] ?? 0 }},
                        {{ $attendanceStats['absent'] ?? 0 }},
                        {{ $attendanceStats['late'] ?? 0 }},
                        {{ $attendanceStats['justified'] ?? 0 }},
                        {{ $attendanceStats['excused'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745', '#dc3545', '#ffc107', '#20c997', '#6c757d'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 5 }
                    }
                }
            }
        });
    });
</script>

@endsection
