<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use App\Models\Session;
use App\Models\SessionTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GenerateSemesterSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartclass:generate-semester-sessions {semester_id} {semester_start_date} {semester_end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows the generation of full sessions for a given semester';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startInput = $this->argument('semester_start_date');
        $endInput = $this->argument('semester_end_date');
        $semesterId = $this->argument('semester_id');

        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        try {

            $startDate = Carbon::parse($startInput)->startOfWeek();
            $endDate = Carbon::parse($endInput)->endOfWeek();

        } catch (Exception $e) {
            $this->error("Invalid date format. Please use YYYY-MM-DD.");
            return;
        }

        // load only templates related to our semester
        $templates = SessionTemplate::with([
            'classe.group.section'
        ])
        ->where('status', 'active')
        ->get()
        ->filter(function ($template) use ($semesterId) {
            return optional($template->classe->group->section)->semester_id == $semesterId;
        });

        $holidays = Holiday::all();

        while ($startDate->lte($endDate)) {
            foreach ($templates as $template) {
                $targetDate = $startDate->copy()->addDays($template->weekday);

                // skip target date if it falls within the range on any holiday
                $isHoliday = $holidays->contains(function ($holiday) use ($targetDate) {
                    return $targetDate->between($holiday->start_date, $holiday->end_date);
                });

                if($isHoliday) continue;

                // skip if session already exists
                $exists = Session::where('classe_id', $template->classe_id)
                    ->where('session_date', $targetDate->toDateString())
                    ->where('start_time', $template->start_time)
                    ->where('end_time', $template->end_time)
                    ->exists();

                if (!$exists) {
                    Session::create([
                        'classe_id' => $template->classe_id,
                        'session_date' => $targetDate->toDateString(),
                        'start_time' => $template->start_time,
                        'end_time' => $template->end_time,
                        'location' => $template->location,
                        'notes' => $template->notes ?? '',
                        'status' => 'scheduled',
                        'type' => $template->type,
                    ]);
                }
            }

            $startDate->addWeek();
        }

        $this->info("Semester sessions generated successfully.");
    }
}
