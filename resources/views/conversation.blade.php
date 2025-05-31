@extends(Auth::user()->role . '.layouts.' . Auth::user()->role)

@section('title', 'Conversation: ' . $conversation->subject)

@section('content')
<div class="container-fluid bg-white p-4 border">
    <div class="page-title">
        📨 Conversation: <strong>{{ $conversation->subject }}</strong>
    </div>

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

    <!-- Messages -->
    <div class="mb-4">
        @foreach($conversation->messages as $message)
            @php
                $isOwn = $message->sender_id === Auth::id();
                $files = $message->studentFiles->merge($message->teacherFiles);
                $roleBadge = ucfirst($message->sender->role);
                $roleColor = match($message->sender->role) {
                    'student' => 'success',
                    'teacher' => 'secondary',
                    'admin' => 'dark',
                    default => 'light'
                };
            @endphp

            <div class="mb-3 p-3 rounded {{ $isOwn ? 'bg-primary-subtle text-white' : 'bg-light' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>{{ $message->sender->full_name }}</strong>
                        <span class="badge text-white bg-{{ $roleColor }} ms-2">{{ $roleBadge }}</span>
                    </div>
                    <small class="{{ $isOwn ? 'text-white-50' : 'text-muted' }}">
                        {{ $message->created_at->diffForHumans() }}
                    </small>
                </div>

                <p class="mb-1">{!! nl2br(e($message->body)) !!}</p>

                @if ($files->count())
                    <div class="mt-2">
                        <h6 class="d-block mb-1 mt-3">📎 Attachments:</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($files as $file)
                                @php
                                    $fileName = basename($file->file_path);
                                    $fileUrl = asset('storage/' . $file->file_path);
                                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                @endphp

                                <a href="{{ $fileUrl }}" target="_blank" class="file-pill">
                                    <i class="bi bi-paperclip"></i>
                                    <span>{{ $fileName }}</span>
                                    <span class="badge bg-warning border text-muted text-uppercase">{{ $extension }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Reply Form -->
    <form action="{{ route(Auth::user()->role . '_reply_conversation', $conversation->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label for="body" class="form-label">✍️ Reply</label>
            <textarea name="body" id="body" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group mb-4">
            <label for="files" class="form-label">📁 Attach Files (optional)</label>
            <input type="file" name="files[]" id="files" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.webp">
            <small class="form-text text-muted">Allowed: PDF, DOC, JPG, PNG, WEBP (Max 10MB each)</small>
        </div>

        <div class="form-group text-end">
            <button type="submit" class="btn btn-primary">Send Reply</button>
        </div>
    </form>
</div>
@endsection
