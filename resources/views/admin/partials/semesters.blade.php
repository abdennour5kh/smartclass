
<!-- Form to add a new semester -->
<form action="{{ route('admin_store_semester') }}" method="POST" class="mb-3">
     @csrf 
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Add new semester (e.g., Semester 1)" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <select name="promotion_id" id="promotion" class="form-control promotion-select" required>
                <option value="">Select Promotion</option> 
                    @foreach($promotions as $promotion) 
                        <option value="{{ $promotion->id }}">{{ $promotion->name }}</option> 
                    @endforeach
                </select>
            </div>
        </div>
  </div>
  <button type="submit" class="btn btn-primary">Add</button>
</form>

@if (!empty($promotions))
    <div class="">
        <div class="list-group">

            @foreach ($promotions as $promo)
                @foreach ($promo->semesters as $semester)
                    <!-- Item -->
                    <div class="list-group-item structureListGroupItem" data-promotion="{{ $promo->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-muted m-0">{{ $semester->name }} - {{ $promo->name }}</h6>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <a href="#" class="btn btn-inverse-warning btn-sm mr-2 select-semester-btn"
                                    data-id="{{ $semester->id }}" data-name="{{ $semester->name }}"
                                    data-promotion="{{ $promo->name }}" >select</a>
                                    <button class="btn btn-sm btn-inverse-secondary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#semesterDetails{{ $semester->id }}"
                                        aria-expanded="false">
                                        details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-2" id="semesterDetails{{ $semester->id }}">
                            <div class="d-flex flex-wrap justify-content-between mt-3 mb-2">
                                <p class="mb-1">🗓 Creation Date: {{ $semester->created_at }}</p>
                                <p class="mb-1">🗓 Last Edited: {{ $semester->updated_at }}</p>
                            </div>
                            <p class="text-muted">Sections:</p>
                            <div class="d-flex">
                                @if (count($semester->sections) > 0)
                                    <ul>
                                        @foreach ($semester->sections as $section)
                                            <li>{{ $section->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>no sections yet !</p>
                                @endif
                            </div>
                            <p class="text-muted">Edit Semester Name:</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="{{ route('admin_update_semester', $semester->id) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $semester->name ?? '') }}">
                                            <input type="hidden" name="promo_id" value="{{ $promo->id }}">
                                            <button type="submit" class="btn btn-primary rounded">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

        </div>
    </div>
@else
    <!-- leave empty -->
@endif


