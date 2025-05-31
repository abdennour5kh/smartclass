
<!-- Form to add a new semester -->
<form action="{{ route('admin_store_section') }}" method="POST" class="mb-3">
     @csrf 
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Add new section (e.g., Section A)" required>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <select name="promotion_id" id="promotion" class="form-control promotion-select" required>
                <option value="">Select Promotion</option> 
                    @foreach($promotions as $promotion) 
                        <option value="{{ $promotion->id }}">{{ $promotion->name }}</option> 
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                                
                                <select name="semester_id" id="semester" class="form-control semester-select" required>
                                    <option value="">Select Semester</option>
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
                    @foreach ($semester->sections as $section)
                        <!-- Item -->
                    <div class="list-group-item structureListGroupItem" data-semester="{{ $semester->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-muted m-0">{{ $section->name }} - {{ $semester->name }} - {{ $promo->name }}</h6>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <a href="#" class="btn btn-inverse-warning btn-sm mr-2 select-section-btn"
                                    data-id="{{ $section->id }}" data-name="{{ $section->name }}"
                                    data-semester="{{ $semester->name }}" data-promotion="{{ $promo->name }}" >select</a>
                                    <button class="btn btn-sm btn-inverse-secondary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#semesterDetails{{ $section->id }}"
                                        aria-expanded="false">
                                        details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-2" id="semesterDetails{{ $section->id }}">
                            <div class="d-flex flex-wrap justify-content-between mt-3 mb-2">
                                <p class="mb-1">🗓 Creation Date: {{ $section->created_at }}</p>
                                <p class="mb-1">🗓 Last Edited: {{ $section->updated_at }}</p>
                            </div>
                            <p class="text-muted">Groups:</p>
                            <div class="d-flex">
                                @if (count($section->groups) > 0)
                                    <ul>
                                        @foreach ($section->groups as $group)
                                            <li>{{ $group->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>no groups yet !</p>
                                @endif
                            </div>
                            <p class="text-muted">Edit Section Name:</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="{{ route('admin_update_section', $section->id) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $section->name ?? '') }}">
                                            <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                                            <button type="submit" class="btn btn-primary rounded">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @endforeach

        </div>
    </div>
@else
    <!-- leave empty -->
@endif


