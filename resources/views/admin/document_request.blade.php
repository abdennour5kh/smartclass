@extends('admin.layouts.admin')
@section('title', 'Document Requests')

@section('content')
<div class="container-fluid bg-white p-4 border rounded-4 shadow-sm">
    <div class="mb-4">
        <h4 class="fw-bold">📄 Document Requests</h4>
        <p class="text-muted">Manage all student document requests here.</p>
    </div>

    <!-- Search Bar -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="requestSearch" class="form-control" placeholder="🔍 Search by student name or document type...">
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle" id="smartClassTable">
            <thead class="table-dark">
                <tr>
                    <th>👤 Student</th>
                    <th>📑 Document</th>
                    <th>📂 Status</th>
                    <th>🕓 Requested At</th>
                    <th>📥 File</th>
                    <th>🛠 Actions</th>
                </tr>
            </thead>
            <tbody id="requestsTable">
                @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->student->first_name }} {{ $request->student->last_name }}</td>
                    <td>{{ $request->document_type }}</td>
                    <td>
                        @php
                            $badge = match($request->status) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'warning'
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($request->status) }}</span>
                    </td>
                    <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        @if ($request->document_path)
                            <a href="{{ asset('storage/' . $request->document_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">📄 Download</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($request->status === 'pending')
                        <button class="btn btn-success  me-2" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">✅ Approve</button>
                        <button class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">❌ Reject</button>
                        @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>✔️ Handled</button>
                        @endif
                    </td>
                </tr>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin_approve_document', $request->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">✅ Approve Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-3">Please upload the generated document file.</p>
                                    <input type="file" class="form-control" name="document_file" required>
                                    <label class="form-label mt-3">Optional message to student:</label>
                                    <textarea name="admin_response" class="form-control" rows="2" placeholder="e.g., Your document is ready."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin_reject_document', $request->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">❌ Reject Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-2">Please explain why this request is being rejected:</p>
                                    <textarea name="admin_response" class="form-control" rows="3" placeholder="e.g., Incomplete information, please try again."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('requestSearch').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    document.querySelectorAll('#requestsTable tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(search) ? '' : 'none';
    });
});
</script>
@endsection
