@extends('student.layouts.student')

@section('title', 'Justifications')

@section('content')
<div class="container-fluid bg-white p-4 border">
    <div class="page-title">
        📝 Absence Justifications
    </div>
    <p class="text-muted mb-3">Here you will find all absence/justifications records, <strong>Keep it empty!</strong></p>

    <div class="table-responsive">
        <table class="w-100 table table-bordered table-hover table-striped" id="smartClassTable">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Module</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absences as $absence)
                    <tr>
                        <td>{{ $absence->session->session_date }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($absence->session->start_time)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($absence->session->end_time)->format('H:i') }}
                        </td>
                        <td>{{ $absence->session->classe->module->name }}</td>
                        <td>{{ $absence->session->classe->group->name }}</td>
                        <td>
                            @if (!$absence->justification)
                                <span class="badge bg-danger">Unjustified</span>
                            @elseif ($absence->justification->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($absence->justification->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif ($absence->justification->status === 'refused')
                                <span class="badge bg-danger">Refused</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </td>
                        <td>
                            @if (!$absence->justification)
                                <button class="btn btn-sm btn-primary justify-btn"
                                        data-id="{{ $absence->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#justifyModal">
                                    Justify
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-secondary view-btn"
                                        data-id="{{ $absence->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewJustificationModal">
                                    View
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Justify Modal -->
<div class="modal fade" id="justifyModal" tabindex="-1" aria-labelledby="justifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="justifyForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="justifyModalLabel">Submit Justification</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="attendance_id" id="attendance_id">

                    <div class="form-group mb-3">
                        <label for="message" class="form-label">Explanation</label>
                        <textarea class="form-control" name="message" id="message" rows="4" placeholder="Write your justification here..." required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="files" class="form-label">Upload supporting documents</label>
                        <small class="text-muted d-block mb-1">Allowed: PDF, JPG, PNG, DOC, DOCX, WEBP (max 10MB each)</small>
                        <input class="form-control" type="file" name="files[]" id="files" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.webp">
                    </div>

                    <div id="loadingSpinner" class="text-center my-3 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Justification</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Justification Modal -->
<div class="modal fade" id="viewJustificationModal" tabindex="-1" aria-labelledby="viewJustificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="viewJustificationModalLabel">Justification Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="viewLoadingSpinner" class="text-center my-3 d-none">
                    <div class="spinner-border text-secondary" role="status"></div>
                </div>

                <div id="justificationDetails" class="d-none">
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span id="justificationStatus" class="badge"></span>
                    </div>

                    <div class="mb-3">
                        <strong>Teacher Decision:</strong>
                        <span id="teacherDecision" class="badge"></span>
                    </div>

                    <div class="mb-3">
                        <strong>Administration Decision:</strong>
                        <span id="adminDecision" class="badge"></span>
                    </div>

                    <div class="mb-3">
                        <strong>Message:</strong>
                        <p id="justificationMessage" class="text-muted mb-0"></p>
                    </div>

                    <div class="mb-3">
                        <strong>Attached Files:</strong>
                        <div id="justificationFiles"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const storeJustification = "{{ route('student_store_justification') }}";
</script>
@endsection
