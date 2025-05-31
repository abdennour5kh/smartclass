@extends('student.layouts.student')

@section('title', 'Schedule')

@section('content')
<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    Schedule
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Time Slot</th>
                                @foreach ($weekDays as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timeSlots as $slot)
                                <tr>
                                    <td><strong>{{ $slot }}</strong></td>
                                    @foreach ($weekDays as $day)
                                        <td class="schedule-cell" data-color="{{ $schedule[$day][$slot]['module']->color ?? '' }}">
                                            @if (!empty($schedule[$day][$slot]))
                                                <div><strong>{{ $schedule[$day][$slot]['module']->name }}</strong></div>
                                                <div>{{ $schedule[$day][$slot]['teacher'] }}</div>
                                                <div >{{ $schedule[$day][$slot]['location'] }}</div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection