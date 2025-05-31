<!-- Form to add new class -->
 <form action="{{ route('admin_store_class') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="input-group">
                <select name="class_type" id="class_type" class="form-control">
                    <option value="">Select Class Type</option>
                    <option value="tp">TP</option>
                    <option value="td">TD</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <select name="promotion_id" id="promotion" class="form-control promotion-select" required>
                <option value="">Select Promotion</option> 
                    @foreach($promotions as $promotion) 
                        <option value="{{ $promotion->id }}">{{ $promotion->name }}</option> 
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <select name="semester_id" id="semester" class="form-control semester-select" required>
                    <option value="">Select Semester</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <select name="section_id" id="section" class="form-control section-select" required>
                    <option value="">Select Section</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Place <hr> outside of .row -->
    <hr class="my-4">

    <div class="container-fluid">
        <div class="row gx-5">
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #eff1f2;">
                    <h6 class="mb-4">Who will teach?</h6>
                    @foreach ($teachers as $teacher)
                    <div class="form-check" style="padding-left: 1.25rem !important;">
                        <input class="form-check-input" type="radio" name="teacher_id" id="teacher_{{ $teacher->id }}" value="{{ $teacher->id }}">
                        <label class="form-check-label ml-0" for="teacher_{{ $teacher->id }}">
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-4 group-column">
                <div class="p-3 rounded" style="background: #eff1f2;">
                    <h6 class="mb-4">To which Group?</h6>
                        <div id="group-options">
                            <p class="text-muted">Please select a semester first.</p>
                        </div>
                </div>
            </div>

            <div class="col-md-4 module-column">
                <div class="p-3 rounded" style="background: #eff1f2;">
                    <h6 class="mb-4">which Module ?</h6>
                    <div id="module-options">
                        <p class="text-muted">Please select a semester first.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-primary mt-5" type="submit">Create</button>

 </form>