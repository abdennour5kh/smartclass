<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use App\Models\Session;
use App\Models\SessionTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartclass:generate-weekly-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows the generation of weekly sessions through active templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // week starts on SATURDAY
        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        Carbon::setWeekEndsAt(Carbon::THURSDAY);

        $templates = SessionTemplate::where('status', 'active')->get();

        // if there is no sessions yet, we fall back to the current week
        $lastSessionDate = Session::max('session_date');
        $startDate = $lastSessionDate ? Carbon::parse($lastSessionDate)->startOfWeek()->addWeek() : Carbon::now()->startOfWeek();

        $endDate = Carbon::now()->endOfWeek();

        $holidays = Holiday::all();

        // loop from $startDate to $endDate week by week
        while ($startDate->lte($endDate)) {
            foreach ($templates as $template) {
                $targetDate = $startDate->copy()->addDays($template->weekday);

                // skip target date if it falls within the range on any holiday
                $isHoliday = $holidays->contains(function ($holiday) use ($targetDate) {
                    return $targetDate->between($holiday->start_date, $holiday->end_date);
                });

                if($isHoliday) continue;

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

            $startDate->addWeek(); // move to next week
        }

        $this->info("Weekly sessions generated successfully.");
    }
}
