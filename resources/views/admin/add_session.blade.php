@extends('admin.layouts.admin')

@section('title', 'Add Session Maniualy')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">🛠️ Edit Session Information</h4>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        ⚠️ Please fix the following issues:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin_store_session') }}" method="POST">
    @csrf
    <input type="hidden" name="classe_id" value="{{ $classId }}">

    <div class="mb-3">
        <label for="session_date" class="form-label">📅 Date</label>
        <input type="date" class="form-control" id="session_date" name="session_date" required >
    </div>

    <div class="mb-3">
        <label for="start_time" class="form-label">🕘 Start Time</label>
        <input type="time" class="form-control" id="start_time" name="start_time" required >
    </div>

    <div class="mb-3">
        <label for="end_time" class="form-label">🕔 End Time</label>
        <input type="time" class="form-control" id="end_time" name="end_time" required >
    </div>

    <div class="mb-3">
        <label for="location" class="form-label">📍 Location</label>
        <input type="text" class="form-control" id="location" name="location" maxlength="20" required >
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">📚 Type</label>
        <input type="text" class="form-control" id="type" name="type" maxlength="3" required >
    </div>

    <div class="mb-3">
    <label for="status" class="form-label">✅ Status</label>
    <select class="form-control" id="status" name="status" required>
        <option value="scheduled" >📅 Scheduled</option>
        <option value="completed" >✅ Completed</option>
        <option value="canceled" >❌ Canceled</option>
        <option value="rescheduled" >🔁 Rescheduled</option>
    </select>
</div>


    <div class="mb-3">
        <label for="notes" class="form-label">📝 Notes (optional)</label>
        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">💾 Save Session</button>
</form>


            </div>
        </div>
    </div>
</div>
@endsection

