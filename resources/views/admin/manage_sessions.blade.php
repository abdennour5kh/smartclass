@extends('admin.layouts.admin')
@section('title', 'Manage Sessions')

@section('content')
<style>
.status-dot {
    width: 12px;
    height: 12px;
    display: inline-block;
    border: 2px solid white;
}

/* Optional: red status for inactive */
.bg-danger.status-dot {
    background-color: #dc3545 !important;
}
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}
.card-header {
    background: linear-gradient(to right, #007bff, #0056b3);
}
</style>

<div class="container-fluid bg-white" style="padding: 1.5rem 1.875rem !important;border: 1px solid #e7eaed !important;">
    <div class="page-title">
        🕒 Manage Sessions
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
    <!-- Filters -->
     <div class="row">
        <div class="col-md-12 mb-2">
            <input type="text" id="searchBar" class="form-control" placeholder="🔍 Search class by module name">
        </div>
        <div class="col">
            <select id="promotion" class="form-control select2 promotion-select">
            <option value="">📚 All Promotions</option>
            @foreach($promotions as $promotion)
                <option value="{{ $promotion->id }}">{{ $promotion->name }}</option>
            @endforeach
            </select>
        </div>
        <div class="col">
            <select id="semester" class="form-control select2 semester-select">
            <option value="">🗓️ All Semesters</option>
            </select>
        </div>
        <div class="col">
            <select id="section" class="form-control select2 section-select">
            <option value="">🏛️ All Sections</option>
            </select>
        </div>
        <div class="col">
            <select id="group" class="form-control select2 group-select">
            <option value="">👥 All Groups</option>
            </select>
        </div>
     </div>

     <hr class="my-4">

     <!-- Spinner -->
    <div id="loadingSpinner" class="text-center my-3" style="display:none;">
    <div class="spinner-border text-primary" role="status"></div>
    </div>

    <!-- Results -->
    <div id="resultsContainer">
        
    </div>

    <!-- Load More -->
    <div class="text-center my-3">
    <button id="loadMoreBtn" class="btn btn-outline-primary d-none">⬇️ Load more</button>
    </div>
</div>

<!-- Session Template Modal -->
<div class="modal fade" id="classeSessionModal" tabindex="-1" aria-labelledby="classeSessionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 shadow-sm">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title" id="classeSessionModalLabel">📅 Session Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
            <!-- Spinner -->
            <div id="classeSessionModalSpinner" class="text-center my-3">
            <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div class="template-form p-4">

            </div>
            <hr class="my-4">

            <div class="sessions-table">
                
            </div>
      </div>
    </div>
  </div>
</div>

<script>


</script>

@endsection