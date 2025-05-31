@extends('student.layouts.student')

@section('title', 'Dashboard')

@section('content')
@if ($announcements->count() > 0)
<div id="ac-bord" style="margin-bottom: 15px;">
    <!-- Swiper Container -->
    <div class="swiper announcement">
        <h4 class="text-white m-0 p-1" style="font-weight: bold;font-size: 30px;padding-left: 10px !important;padding-top: 13px !important;">Classe Announcements :</h4>
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            @foreach ($announcements as $ac)
            <div class="swiper-slide">
                <div class="card bg-transparent">
                    <div class="card-body text-white p-1">
                        <h6 class="card-title text-white m-0" style="font-weight: initial;padding: 10px !important;padding-top: 20px !important;">
                            Prof <strong>{{ $ac->Classe->teacher->first_name }} {{ $ac->Classe->teacher->last_name }}</strong> | at <strong>{{ $ac->created_at->format('h:i A d/m/Y') }}</strong>
                        </h6>
                        <p class="card-description text-white m-0" style="padding-left: 10px !important;padding-top: 10px !important;">
                            {!! nl2br(e($ac->content)) !!}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="swiper-pagination"></div>

    </div>
    <style>
        .announcement {
            padding: 10px;
            background: url('{{ asset("images/ac_background.jpg") }}') no-repeat center center,
            #7cd175;
            /* This is the fallback color */
            background-size: cover;
            /* Make the image cover the area */
            border-radius: 4px;
        }

        .announcement::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(39, 184, 26, 0.7);
            /* semi-transparent green overlay */
            z-index: 0;
            border-radius: 4px;
        }

        .swiper>* {
            position: relative;
            z-index: 1;
        }

        .swiper {
            position: relative;
            overflow: hidden;
        }

        .swiper-pagination-bullet {
            background: #ffff;
            /* your primary color */
            opacity: 0.4;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
        }

        .swiper-slide {
            width: auto;
        }
    </style>
</div>
@endif
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card" style="margin-bottom: 15px;">
            <div class="card-body">
                <h2 class="card-title d-flex justify-content-between align-items-center">
                    Classes
                    <span class="d-flex gap-0 fs-4">
                        <div id="swiper-prev" class="cursor-pointer"><i class="mdi mdi-chevron-left" style="font-size: 20px;"></i></div>
                        <div id="swiper-next" class="cursor-pointer"><i class="mdi mdi-chevron-right" style="font-size: 20px;"></i></div>
                    </span>
                </h2>
                @if (count($classesReport) > 0)
                <div class="swiper" id="classes">
                    <div class="swiper-wrapper">
                        @foreach ($classesReport as $class)
                        <!-- Slide 1 -->
                        <div class="swiper-slide">
                            <a href="{{ route('student_class_details', $class['id']) }}" class="text-decoration-none">
                                <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                                    <img src="{{ $class['module']->img_url ? asset('storage/' . $class['module']->img_url) : asset('images/default-image.jpg') }}"
                                        class="card-img-top card-img-class"
                                        alt="Classe Image"
                                        style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-0 pt-3">
                                        <div class="class-card">
                                            <span class="badge class-badge mb-3 mt-1">{{ $class['teacher'] }}</span>
                                            <span class="badge class-badge mb-3 mt-1">{{ $class['type'] }}</span>
                                        </div>

                                        <h5 class="card-title mb-3">{{ $class['module']->name }}</h5>


                                        <!-- Progress Bar -->
                                        <div class="progress" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar " id="classesProgressBar" role="progressbar" style="width: 40%;" aria-valuenow="{{ $class['attendanceRate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach


                    </div>
                </div>
                @else
                <p>Classes has not started yet.</p>
                @endif


            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">
                    Tasks
                </h2>
                <p class="card-description">
                    Keep up the hard work
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="smartClassTable">
                        <thead>
                            <tr>
                                <th>📘 Module</th>
                                <th>📝 Task Title</th>
                                <th>📅 Deadline</th>
                                <th>✅ Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                            <tr>
                                <td>{{$task->classe->module->name}}</td>
                                <td><a class="text-primary" href="{{ route('student_submit_task', $task->id) }}">{{$task->title}}</a></td>
                                <td>{{$task->deadline}}</i></td>
                                @php
                                $submission = $task->submissions->first(); // because we already filtered for current student
                                @endphp

                                @if ($submission)
                                @if ($submission->status === 'pending')
                                <td><label class="badge badge-warning">Pending</label></td>
                                @elseif ($submission->status === 'approved')
                                <td><label class="badge badge-success">Approved</label></td>
                                @elseif ($submission->status === 'refused')
                                <td><label class="badge badge-danger">Refused</label></td>
                                @endif
                                @else
                                <td><label class="badge badge-secondary">Not Submitted</label></td>
                                @endif
                            </tr>
                            @empty
                            <td colspan="4">No Tasks for the moment, stay ready.</td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = [
            '#4ebe38', '#007bff', '#ffc107', '#e83e8c', '#20c997', '#6610f2',
            '#fd7e14', '#6f42c1', '#17a2b8', '#dc3545', '#28a745', '#ff6f61',
            '#00b894', '#0984e3', '#e17055', '#b71540', '#8e44ad', '#2ecc71',
            '#f39c12', '#1abc9c', '#c0392b', '#6c5ce7', '#d63031'
        ];
        // Loop through each class card
        document.querySelectorAll('.class-card').forEach(card => {
            const color = colors[Math.floor(Math.random() * colors.length)];
            const transparent = color + '42';

            // Apply color to all badges inside this class card
            card.querySelectorAll('.class-badge').forEach(badge => {
                badge.style.backgroundColor = transparent;
                badge.style.color = color;
            });
        });
    });
</script>


@endsection