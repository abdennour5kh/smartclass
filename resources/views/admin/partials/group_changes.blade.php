<!-- Group Change Requests List -->
<div class="list-group">
    @forelse ($groupChanges as $request)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start flex-column flex-md-row">
                <div>
                    <div class="mt-2 mt-md-0">
                        <h6 class="text-muted mb-0">
                            <strong>{{ $request->student->first_name }} {{ $request->student->last_name }}</strong>
                            requested to change group
                            <strong>from {{ $request->student->group->name }}</strong>
                            to <strong>{{ $request->toGroup->name }}</strong>
                        </h6>
                    </div>
                </div>
                <div class="mt-2 mt-md-0 text-md-end">
                    <form action="{{ route('admin_update_group_change') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="change_id" value="{{ $request->id }}">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-sm btn-success me-2">✅ Approve</button>
                    </form>

                    <form action="{{ route('admin_update_group_change') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="change_id" value="{{ $request->id }}">
                        <input type="hidden" name="action" value="refuse">
                        <button type="submit" class="btn btn-sm btn-danger me-2">❌ Refuse</button>
                    </form>

                    <button class="btn btn-sm btn-inverse-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#groupChangeDetails{{ $request->id }}"
                            aria-expanded="false"
                            aria-controls="groupChangeDetails{{ $request->id }}">
                        ⬇️ Details
                    </button>
                </div>
            </div>

            <!-- Expanded details -->
            <div class="collapse mt-3" id="groupChangeDetails{{ $request->id }}">
                <div class="bg-light border rounded p-3">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>👤 Student:</strong> {{ $request->student->first_name }} {{ $request->student->last_name }}</p>
                            <p class="mb-1"><strong>📛 Registration:</strong> {{ $request->student->registration_num }}</p>
                            <p class="mb-1"><strong>🎓 Promotion:</strong> {{ $request->student->group->section->semester->promotion->name }}</p>
                            <p class="mb-1"><strong>📍 Current Group:</strong> {{ $request->student->group->name }}</p>
                            <p class="mb-1"><strong>📌 Requested Group:</strong> {{ $request->toGroup->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>📤 Submitted:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
                            <p class="mb-1"><strong>📅 Status:</strong>
                                @php
                                    $statusClass = match($request->status) {
                                        'approved' => 'success',
                                        'refused' => 'danger',
                                        default => 'warning text-dark',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>📝 Reason:</strong>
                        <div class="border rounded p-2 bg-white mt-1 text-muted">
                            {{ $request->reason ?: 'No reason provided.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="list-group-item text-center py-5 text-muted">
            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
            No group change requests at the moment.
        </div>
    @endforelse
</div>

<!-- View History Button -->
<div class="mt-4 text-end">
    <a href="" class="btn btn-outline-secondary">
        📜 View Full Request History
    </a>
</div>
