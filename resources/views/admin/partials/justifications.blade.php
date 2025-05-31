<style>
.file-pill {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: background-color 0.2s ease-in-out;
}
.file-pill:hover {
    background-color: #e9ecef;
    text-decoration: none;
}
</style>

<!-- Justification List -->
<div class="list-group">
    @forelse ($newJustifications as $justification)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start flex-column flex-md-row">
                <div>
                    <div class="mt-2 mt-md-0">
                        <h6 class="text-muted mb-0">
                            <strong>{{ $justification->student->first_name }} {{ $justification->student->last_name }}</strong>
                            submitted a justification for
                            <strong>{{ $justification->session->classe->module->name }}</strong>
                            ({{ $justification->session->session_date }})
                        </h6>
                    </div>
                </div>
                <div class="mt-2 mt-md-0 text-md-end">
                <form action="{{ route('admin_update_justification') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="justification_id" value="{{ $justification->id }}">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-sm btn-success me-2">✅ Approve</button>
                </form>

                <form action="{{ route('admin_update_justification') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="justification_id" value="{{ $justification->id }}">
                    <input type="hidden" name="action" value="refuse">
                    <button type="submit" class="btn btn-sm btn-danger me-2">❌ Refuse</button>
                </form>

                    <button class="btn btn-sm btn-inverse-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#justificationDetails{{ $justification->id }}"
                            aria-expanded="false"
                            aria-controls="justificationDetails{{ $justification->id }}">
                        ⬇️ Details
                    </button>
                </div>
            </div>

            <!-- Expanded details -->
            <div class="collapse mt-3" id="justificationDetails{{ $justification->id }}">
                <div class="bg-light border rounded p-3">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>👤 Student:</strong> {{ $justification->student->first_name }} {{ $justification->student->last_name }}</p>
                            <p class="mb-1"><strong>👥 Group:</strong> {{ $justification->session->classe->group->name }}</p>
                            <p class="mb-1">
                                <strong>📚 Module:</strong>
                                {{ $justification->session->classe->module->name }},
                                {{ $justification->session->classe->class_type }}
                            </p>

                            <p class="mb-1">
                                <strong>🧑‍🏫 Teacher:</strong>
                                {{ $justification->session->classe->teacher->first_name }} {{ $justification->session->classe->teacher->last_name }}
                            </p>

                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>🗓 Session Date:</strong> {{ $justification->session->session_date }}</p>
                            <p class="mb-1"><strong>📤 Submitted:</strong> {{ $justification->created_at->format('Y-m-d H:i') }}</p>
                            <p class="mb-1"><strong>🧑‍🏫 Teacher Decision:</strong>
                                @php
                                    $teacherStatus = $justification->teacher_decision;
                                    $teacherLabel = 'Not Reviewed';
                                    $teacherClass = 'secondary';

                                    if ($teacherStatus === '1') {
                                        $teacherLabel = 'Approved';
                                        $teacherClass = 'success';
                                    } elseif ($teacherStatus === '0') {
                                        $teacherLabel = 'Rejected';
                                        $teacherClass = 'danger';
                                    } elseif ($teacherStatus === '2') {
                                        $teacherLabel = 'Under Review';
                                        $teacherClass = 'warning text-dark';
                                    }
                                @endphp
                                <span class="badge bg-{{ $teacherClass }}">{{ $teacherLabel }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>📝 Full message:</strong>
                        <div class="border rounded p-2 bg-white mt-1 text-muted">
                            {{ $justification->message ?: 'No message.' }}
                        </div>
                    </div>

                    <div>
                        <strong>📎 Attached Files:</strong>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse ($justification->files as $file)
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
                            @empty
                                <em class="text-muted">No files uploaded.</em>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="list-group-item text-center py-5 text-muted">
            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
            No justification requests at the moment.
        </div>
    @endforelse
</div>

<!-- View History Button -->
<div class="mt-4 text-start">
    <a href="" class="btn btn-outline-secondary">
        📜 View Full Justification History
    </a>
</div>
