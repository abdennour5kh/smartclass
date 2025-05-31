
<p class="text-muted">You can add new Promotion, or select Promotion to add Semester.</p>
<!-- Form to add a new promotion -->
<form action="{{ route('admin_store_promotion') }}" method="POST" class="mb-3">
    @csrf
    <div class="input-group">
        <input type="text" name="name" class="form-control" placeholder="Add new promotion (e.g., Licence 1ère année)" required>
        <button type="submit" class="btn btn-primary">Add</button>
    </div>
</form>

@if (!empty($promotions))
<div class="">
    <div class="list-group">

        @foreach ($promotions as $promo)
            <!-- Promotion 1 -->
        <div class="list-group-item structureListGroupItem" data-promotion="{{ $promo->id }}">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="text-muted m-0">{{ $promo->name }}</h6>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end">
                        <a href="" class="btn btn-inverse-warning btn-sm mr-2 select-promotion-btn"
                        data-id="{{ $promo->id }}" data-name="{{ $promo->name }}" >select</a>
                        <button class="btn btn-sm btn-inverse-secondary" data-bs-toggle="collapse" data-bs-target="#semester1Details{{ $promo->id }}" aria-expanded="false">
                        details
                        </button>
                        
                    </div>
                </div>
            </div>
            <div class="collapse mt-2" id="semester1Details{{ $promo->id }}">
                <div class="d-flex flex-wrap justify-content-between mt-3 mb-2">
                <p class="mb-1">🗓 Creation Date: {{ $promo->created_at }}</p>
                <p class="mb-1">🗓 Last Edited: {{ $promo->updated_at }}</p>
                </div>
                <p class="text-muted">Semesters:</p>
                <div class="d-flex">
                                @if (count($promo->semesters) > 0)
                                    <ul>
                                        @foreach ($promo->semesters as $semester)
                                            <li>{{ $semester->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>no semesters yet !</p>
                                @endif
                            </div>
                <p class="text-muted">Edit Promotion Name:</p>
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('admin_update_promotion', $promo->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="name" class="form-control" value="{{ old('name', $promo->name ?? '') }}">
                                <button type="submit" class="btn btn-primary rounded">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    

    </div>
</div>
@else
    <!-- leave empty -->
@endif

