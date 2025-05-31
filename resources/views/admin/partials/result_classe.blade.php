@if($classes->isEmpty())
    <p>Sorry, there are no classes for the selected group. Please create a class first: 
        <strong><a href="{{ route('admin_academic_structure') }}">Create Class</a></strong>
    </p>
@else
    <div class="row">
        @foreach($classes as $class)
            <div class="col-md-4 mb-4">
                <div class="card classe-card shadow-sm rounded-4 h-100" style="cursor: pointer;" data-id="{{ $class->id }}">
                    <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            🧠 {{ $class->module->name }} – Group {{ $class->group->name }}
                        </h5>
                        @if ($class->sessionTemplate)
                            @if ($class->sessionTemplate->status == 'active')
                                <span 
                                    class="status-dot bg-success rounded-circle" 
                                    data-bs-toggle="tooltip" 
                                    title="🟢 Active – Template is running"
                                ></span>
                            @endif
                        @else
                            <span 
                                class="status-dot bg-danger rounded-circle" 
                                data-bs-toggle="tooltip" 
                                title="🔴 Inactive – Template is Missing or inactive"
                            ></span>
                        @endif
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>📚 Module:</strong> {{ $class->module->name }}</li>
                            <li><strong>👥 Group:</strong> {{ $class->group->name }}</li>
                            <li><strong>🏛️ Section:</strong> {{ $class->group->section->name }}</li>
                            <li><strong>🎓 Promotion:</strong> {{ $class->group->section->semester->promotion->name }}</li>
                            <li><strong>🧑‍🏫 Teacher:</strong> {{ $class->teacher->first_name }} {{ $class->teacher->last_name }}</li>
                            <li><strong>🧪 Type:</strong> {{ $class->class_type }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
