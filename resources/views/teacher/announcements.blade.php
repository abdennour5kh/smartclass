@extends('teacher.layouts.teacher')
@section('title', 'Announcements')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    📢 Announcements
                </div>
                @if (session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
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
                <form class="forms-sample" method="POST" action="{{ route('teacher_announcement_add') }}">
                    @csrf
                    @if ($classes)
                        <div class="form-group">
                            <label for="exampleFormControlSelect3">Select Group & Module</label>
                            <select name="classes_id" class="form-control form-control-sm" id="exampleFormControlSelect3" required>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->group->name }} - {{ $classe->module->name }}</option>
                                @endforeach
                            
                            </select>
                        </div>
                    @elseif ($classe)
                    <div class="form-group">
                            <label for="exampleFormControlSelect3">Select Group & Module</label>
                            <select name="classes_id" class="form-control form-control-sm" id="exampleFormControlSelect3" required>
                               
                                    <option value="{{ $classe->id }}" selected>{{ $classe->group->name }} - {{ $classe->module->name }}</option>
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                      <label for="exampleTextarea1">Announcement Content</label>
                      <textarea name="content" class="form-control" id="exampleTextarea1" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                </form>
                <hr class="my-5">
                <div class="card-title">
                    Your Announcements
                </div>  
                @if($announcements->count() > 0)
                    <div class="list-group">
                        @foreach($announcements as $announcement)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $announcement->group->name }} - {{ $announcement->module->name }}</strong><br>
                                        <small class="text-muted">{{ $announcement->created_at->format('d M Y, H:i') }}</small>
                                        <p class="mb-0 mt-2">{{ $announcement->content }}</p>
                                    </div>
                                    <div class="text-end">
                                    <form action="{{ route('teacher_delete_announcement', $announcement->id) }}" 
                                        method="POST" style="display:inline;" 
                                        onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No announcements posted yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
