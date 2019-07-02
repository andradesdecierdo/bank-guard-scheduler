<?php

namespace App\Services;

use Carbon\Carbon;

class ScheduleService
{
    /**
     * Get the guard's time frames for the given schedule.
     *
     * @param $schedule         - schedule of a guard|Instance of App\Models\Schedule
     * @param $dailyTimeFrames  - array of time frames in a day
     * @return array
     */
    public function getDailyGuardTimeFrames($schedule, $dailyTimeFrames)
    {
        $newTimeFrames = $dailyTimeFrames;
        $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);

        // Add a color to every time frame as indicator of the schedule duration.
        foreach ($dailyTimeFrames as $frameKey => $timeFrame) {
            $currentTime = Carbon::createFromFormat('H:i', $timeFrame);
            // Check if the time frame is between the guard daily schedule.
            if ($currentTime->between($startTime, $endTime, false) ||
                $currentTime->eq($startTime)
            ) {
                $newTimeFrames[$frameKey] = true;
            }
        }

        return $newTimeFrames;
    }

    /**
     * Construct guard schedules based on the given schedule timeline.
     * Plots the guard available schedule time frames for all the displayed dates.
     *
     * @param $guards               - collection of guards with their schedules
     * @param $dates                - array of dates as filters in displaying the guard schedules
     * @param $dailyTimeFrames      - array of time frames in a day
     * @param $dateSecurityChecker  - array of boolean for checking the availability of security guards on each date in $dates
     * @return array
     */
    public function getGuardScheduleTimeline($guards, $dates, $dailyTimeFrames, $dateSecurityChecker)
    {
        $guardSchedules = [];
        foreach ($guards as $key => $guard) {
            $guardSchedules[$key]['id'] = $guard->id;
            $guardSchedules[$key]['name'] = $guard->name;
            $guardSchedules[$key]['color_indicator'] = $guard->color_indicator;
            $guardSchedules[$key]['schedules'] = [];

            foreach ($dates as $dateKey => $day) {
                $schedule = $guard->schedules->where('date', $day)->first();
                $newTimeFrames = $dailyTimeFrames;
                // Set the guard time frames display.
                if ($schedule) {
                    $dateSecurityChecker[$dateKey] = true;
                    $newTimeFrames = $this->getDailyGuardTimeFrames($schedule, $dailyTimeFrames);
                }
                $guardSchedules[$key]['schedules'] = array_merge($guardSchedules[$key]['schedules'], $newTimeFrames);
            }
        }

        return [
            $guardSchedules,
            $dateSecurityChecker,
        ];
    }

    /**
     * Initialize schedule timeline to be displayed
     * based on the number of days and minutes interval.
     *
     * @param $noOfDays         - number of days for schedules to be displayed starting tomorrow
     * @param $minutesInterval  - the number of minutes interval between time frames
     * @return array
     */
    public function initializeScheduleTimeline($noOfDays, $minutesInterval)
    {
        $startDay = Carbon::tomorrow();
        $startTime = $startDay->copy();
        $endDay = $startDay->copy()->addDay($noOfDays);
        $nextDay = $startDay->copy()->addDay(1);

        $dailyTimeFrames = [];
        // Set the daily time frames based on the interval.
        for ($time = $startTime; $time->lt($nextDay); $time->addMinute($minutesInterval)) {
            $dailyTimeFrames[] = $time->format('H:i');
        };

        $dates = [];
        $totalTimeFrames = [];
        $dateSecurityChecker = [];
        for ($day = $startDay; $day->lt($endDay); $day->addDay(1)) {
            // List the number of schedule dates to be displayed.
            $dates[] = $day->toDateString();
            // List the time frames to be displayed for all the dates.
            $totalTimeFrames = array_merge($totalTimeFrames, $dailyTimeFrames);
            // List the availability of security guards for all the dates
            $dateSecurityChecker[] = false;
        }

        return [
            $dates,
            $dailyTimeFrames,
            $totalTimeFrames,
            $dateSecurityChecker,
        ];
    }

}
