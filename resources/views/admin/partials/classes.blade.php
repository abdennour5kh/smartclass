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
                        <p class="text-muted">Please select a section first.</p>
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

@if (!empty($promotions))
<div class="">
        <div class="list-group">
@foreach ($promotions as $promotion)
@foreach ($promotion->semesters as $semester)
@foreach ($semester->sections as $section)
@foreach ($section->groups as $group)
@forelse ($group->classes as $class)
<div class="list-group-item structureListGroupItem moduleListGroup mt-4"
     data-promotion-id="{{ $promotion->id }}"
     data-semester-id="{{ $semester->id }}"
     data-group-id="{{ $group->id }}"
     data-class-id="{{ $class->id }}">
    
    <div class="row align-items-center">
        <div class="col-md-6">
            <h6 class="text-muted m-0">
                {{ strtoupper($class->class_type) }} – {{ $class->module->name }} – {{ $group->name }} – {{ $semester->name }} – {{ $promotion->name }}
            </h6>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex flex-wrap justify-content-md-end">
                <button class="btn btn-sm btn-inverse-secondary"
                        data-bs-toggle="collapse"
                        data-bs-target="#classDetails{{ $class->id }}"
                        aria-expanded="false">
                    details
                </button>
            </div>
        </div>
    </div>

    <div class="collapse mt-2" id="classDetails{{ $class->id }}">
        <div class="d-flex flex-wrap justify-content-between mt-3 mb-2">
            <p class="mb-1">👨‍🏫 Teacher: {{ $class->teacher->first_name }} {{ $class->teacher->last_name }}</p>
            <p class="mb-1">📦 Module: {{ $class->module->name }}</p>
        </div>
        <p class="mb-1">📘 Class Type: {{ $class->class_type }}</p>
        <p class="mb-1">👥 Class Type: {{ $class->group->name }}</p>
        <div class="d-flex flex-wrap justify-content-between mb-2">
            <p class="mb-1">🗓 Created At: {{ $class->created_at }}</p>
            <p class="mb-1">🛠 Last Updated: {{ $class->updated_at }}</p>
        </div>

        <div class="mt-3 pt-3 border-top">
    <form action="{{ route('admin_update_classe', $class) }}" method="POST">
        @csrf

        <div class="row align-items-end">
            <div class="col-md-8">
                <label for="teacher_id_{{ $class->id }}" class="form-control">👨‍🏫 Change Assigned Teacher:</label>
                <select name="teacher_id" id="teacher_id_{{ $class->id }}" class="form-control" required>
                    <option value="">Select a teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $teacher->id == $class->teacher_id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn-sm btn-primary mt-2">
                    🔄 Update Teacher
                </button>
            </div>
        </div>
    </form>
</div>

    </div>
</div>


@empty

@endforelse
@endforeach
@endforeach
@endforeach
@endforeach
</div>
</div>
@endif