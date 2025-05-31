@extends(Auth::user()->role . '.layouts.' . Auth::user()->role)

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid bg-white p-4 border">
    <div class="page-title">
        ✉️ Compose Message
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <form action="" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Recipient Selection -->
        <div class="form-group">
            <label for="recipient_id" class="form-label">To</label>
            <select name="recipient_id" id="recipient_id" class="form-control" required>
                @if ($recipients->count() > 1)
                    <option disabled selected>-- Select recipient --</option>
                @endif
                @foreach($recipients as $recipient)
                    <option value="{{ $recipient->id }}" {{ $recipients->count() === 1 ? 'selected' : '' }}>
                        {{ $recipient->full_name }} ({{ ucfirst($recipient->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Subject -->
        <div class="form-group mt-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>

        <!-- Message Body -->
        <div class="form-group mt-3">
            <label for="body" class="form-label">Message</label>
            <textarea name="body" id="body" class="form-control" rows="5" required></textarea>
        </div>

        <!-- File Attachment -->
        <div class="form-group mt-3">
            <label for="files" class="form-label">Attachments</label>
            <input type="file" name="files[]" id="files" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.webp">
            <small class="text-muted">pdf,jpg,jpeg,png,doc,docx,webp (Max 10MB each)</small>
        </div>

        <div class="form-group mt-4 text-end">
            <button type="submit" class="btn btn-primary">Send</button>
        </div>
    </form>
</div>
@endsection
