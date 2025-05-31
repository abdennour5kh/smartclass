@extends('student.layouts.student')
@section('title', 'Announcement')
@section('content')
<div class="container-fluid bg-white p-4 border rounded">
    <div class="page-title">
        📢 Announcelent - {{$classe->module->name}}
    </div>
    @if($announcements->count() > 0)
    <div class="list-group mt-5">
        @foreach($announcements as $announcement)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $announcement->group->name }} - {{ $announcement->module->name }}</strong><br>
                    <small class="text-muted">{{ $announcement->created_at->format('d M Y, H:i') }}</small>
                    <p class="mb-0 mt-2">{{ $announcement->content }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-muted">No announcements posted yet.</p>
    @endif
</div>
@endsection