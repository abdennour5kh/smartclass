@extends('teacher.layouts.teacher')
@section('title', 'Teacher Dachboard')

@section('content')
<div id="summary">
    <div class="row">
        <div class="col-md-4 h-100">
        <div class="stat-box text-white position-relative overflow-hidden rounded-3 p-4" style="background-color: #864ad0;">
            <div class="z-1 position-relative">
            <h4 class="mb-1">Justifications (5)</h4>
            <p class="fs-4 mb-0">pending jsutifications review</p>
            </div>
            <i class="mdi mdi-file-document stat-icon"></i> <!-- Or use an image if you prefer -->
        </div>
        <div class="card mt-3 stat-card">
            <div class="card-body">
                <p class="card-descriptipn" style="color: #864ad0;">
                    Abdennour Khelfi
                </p>
                <h4 class="card-title">Absent due to a medical condition</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted">March 24, 2025</p>
                    <img src="{{ asset('images/face1.jpg') }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                </div>
            </div>
        </div>
        <div class="card mt-3 stat-card">
            <div class="card-body">
                <p class="card-descriptipn" style="color: #864ad0;">
                    Abdennour Khelfi
                </p>
                <h4 class="card-title">Absent due to a medical condition</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted">March 24, 2025</p>
                    <img src="{{ asset('images/face1.jpg') }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                </div>
            </div>
        </div>
        </div>
        <div class="col-md-4 h-100">
            <div class="stat-box text-white position-relative overflow-hidden rounded-3 p-4" style="background-color: #feac30;">
                <div class="z-1 position-relative">
                <h4 class="mb-1">Tasks (5)</h4>
                <p class="fs-4 mb-0">tasks submitted pending review</p>
                </div>
                <i class="mdi mdi-file-check stat-icon"></i> <!-- Or use an image if you prefer -->
            </div>
            <div class="card mt-3 stat-card">
                <div class="card-body">
                    <p class="card-descriptipn" style="color: #4ebe38;">
                        Abdennour Khelfi
                    </p>
                    <h4 class="card-title">Home work use case diagramms with report</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted">March 24, 2025</p>
                        <img src="{{ asset('images/face1.jpg') }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                    </div>
                    <p class="text-muted"><i class="mdi mdi-folder-upload mr-1"></i>2 Attachments</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 h-100">
        <div class="stat-box text-white position-relative overflow-hidden rounded-3 p-4" style="background-color: #51a6f6;">
            <div class="z-1 position-relative">
            <h4 class="mb-1">Messages (5)</h4>
            <p class="fs-4 mb-0">new unread messages recived</p>
            </div>
            <i class="mdi mdi-email-outline stat-icon"></i> <!-- Or use an image if you prefer -->
        </div>
        <div class="card mt-3 stat-card">
            <div class="card-body stat-empty p-3">
                <div style="padding: 30px;border: 2px #d1d1d1 dashed;border-radius: 30px;">
                    <p class="card-description text-center">Allset!, there no new messages.</p>
                </div>
            </div>
        </div>
        </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card mt-3">
            <div class="card br-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title m-0">
                            My Groups
                        </h2>
                        <a href="#"><p>See All</p></a>
                    </div>
                    <div class="row mt-3">
                    <div class="col">
                        <div class="card br-30" style="background-color: #1668bd;">
                            <div class="card-body">
                                <h6 class="card-title text-white">
                                    Licence, Group 5
                                </h6>
                                <p class="card-description text-white">
                                    Module Security with 37 students
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card br-30" style="background-color: #ff8b00;">
                            <div class="card-body">
                                <h6 class="card-title text-white">
                                    Licence, Group 5
                                </h6>
                                <p class="card-description text-white">
                                    Module Security with 37 students
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card br-30" style="background-color: #ffb318;">
                            <div class="card-body">
                                <h6 class="card-title text-white">
                                    Licence, Group 5
                                </h6>
                                <p class="card-description text-white">
                                    Module Security with 37 students
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card br-30" style="background-color: #349c55;">
                            <div class="card-body">
                                <h6 class="card-title text-white">
                                    Licence, Group 5
                                </h6>
                                <p class="card-description text-white">
                                    Module Security with 37 students
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
<style>
    .stat-box {
  height: 93px;
  /* background-color: #864ad0; */
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  border-radius: 30px;
}

.stat-box h4 {
    font-weight: bold !important;
}

.stat-icon {
  font-size: 80px;
  color: rgba(0, 0, 0, 0.07);
  position: absolute;
  bottom: -10px;
  right: 10px;
}

.stat-card {
    border-radius: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.stat-card h4 {
    font-size: 15px !important;
}

.stat-empty {
    background-color: #e2e2e2;
    border-radius: 30px;

}

.br-30 {
    border-radius: 30px !important;
}
</style>
@endsection