@if (!$template->isEmpty())
    @php 
        $templateData = $template->first();
    @endphp
@else
    @php 
        $templateData = null;
    @endphp
@endif

<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="page-title m-0">
        Create or Edit Class Template
    </div>
    <a href="{{ route('admin_add_session', $classe_id) }}" class="btn btn-primary">
        ➕ Create Session Manually
    </a>
</div>
<p class="text-muted">Template used to automatically generate sessions.</p>

<form id="sessionTemplateForm" method="POST" action="{{ $templateData ? route('admin_update_template', $templateData->id) : route('admin_store_template') }}">
    @csrf

    <input type="hidden" name="classe_id" id="classe_id" value="{{ $templateData->classe_id ?? $classe_id }}">

    <div class="row">
        <!-- Weekday -->
        <div class="form-group col-md-6 mb-3">
            <label for="weekday" class="form-label">Weekday</label>
            <select name="weekday" id="weekday" class="form-control" required>
                <option value="">Choose a day</option>
                @foreach(['0'=>'Sunday','1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday'] as $val => $day)
                    <option value="{{ $val }}" {{ (isset($templateData) && $templateData->weekday == $val) ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
        </div>

        <!-- Time Slot -->
        <div class="form-group col-md-6 mb-3">
            <label for="time_slot" class="form-label">Time Slot</label>
            <select name="time_slot" id="time_slot" class="form-control" required>
                <option value="">Select a time slot</option>
                @php
                    $timeSlots = [
                        '08:00 - 09:30',
                        '09:45 - 11:15',
                        '11:30 - 13:00',
                        '14:00 - 15:30',
                        '15:45 - 17:15',
                    ];

                    $currentSlot = $templateData
                        ? (date('H:i', strtotime($templateData->start_time)) . ' - ' . date('H:i', strtotime($templateData->end_time)))
                        : null;
                @endphp

                @foreach ($timeSlots as $slot)
                    <option value="{{ $slot }}" {{ $slot == $currentSlot ? 'selected' : '' }}>{{ $slot }}</option>
                @endforeach
            </select>
        </div>

        <!-- Location -->
        <div class="form-group col-md-6 mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control"
                   maxlength="20" value="{{ $templateData->location ?? '' }}" required>
        </div>

        <!-- Status -->
        <div class="form-group col-md-6 mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="active" {{ (isset($templateData) && $templateData->status == 'active') ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ (isset($templateData) && $templateData->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Notes -->
        <div class="form-group col-md-12 mb-3">
            <label for="notes" class="form-label">Notes (optional)</label>
            <textarea name="notes" id="notes" class="form-control" rows="2">{{ $templateData->notes ?? '' }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-success">💾 {{ $templateData ? 'Update' : 'Save' }} Template</button>
</form>
