<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Guard;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Carbon\Carbon;

class ScheduleServiceTest extends TestCase
{
    const ARRAY_ASSERTION_ERROR = 'The two arrays are not equal';
    const NO_OF_DAYS_DISPLAY = 3;
    const MINUTES_INTERVAL = 30;
    const DATE_SECURITY_CHECKER = [
        false,
        false,
        false,
    ];
    const DAILY_TIME_FRAMES = [
        0 => "00:00",
        1 => "00:30",
        2 => "01:00",
        3 => "01:30",
        4 => "02:00",
        5 => "02:30",
        6 => "03:00",
        7 => "03:30",
        8 => "04:00",
        9 => "04:30",
        10 => "05:00",
        11 => "05:30",
        12 => "06:00",
        13 => "06:30",
        14 => "07:00",
        15 => "07:30",
        16 => "08:00",
        17 => "08:30",
        18 => "09:00",
        19 => "09:30",
        20 => "10:00",
        21 => "10:30",
        22 => "11:00",
        23 => "11:30",
        24 => "12:00",
        25 => "12:30",
        26 => "13:00",
        27 => "13:30",
        28 => "14:00",
        29 => "14:30",
        30 => "15:00",
        31 => "15:30",
        32 => "16:00",
        33 => "16:30",
        34 => "17:00",
        35 => "17:30",
        36 => "18:00",
        37 => "18:30",
        38 => "19:00",
        39 => "19:30",
        40 => "20:00",
        41 => "20:30",
        42 => "21:00",
        43 => "21:30",
        44 => "22:00",
        45 => "22:30",
        46 => "23:00",
        47 => "23:30",
    ];

    /**
     * Get the list of schedule dates to be displayed for the next ($noOfDays) days.
     *
     * @param int $noOfDays
     * @return array $dates
     */
    private function getDatesDisplay($noOfDays)
    {
        $startDay = Carbon::tomorrow();
        $endDay = $startDay->copy()->addDay($noOfDays);
        $dates = [];
        for ($day = $startDay; $day->lt($endDay); $day->addDay(1)) {
            $dates[] = $day->toDateString();
        }

        return $dates;
    }

    /**
     * Set the total time frames based on a given schedule for (NO_OF_DAYS_DISPLAY) days.
     *
     * @param $schedule
     * @param $key
     * @return array $dates
     */
    private function setIndividualTotalTimeFrames($schedule, $key) {
        $scheduleService = new ScheduleService();
        $totalTimeFrames = [];
        for ($x = 0; $x < self::NO_OF_DAYS_DISPLAY; $x++) {
            $dailyTimeFrames = self::DAILY_TIME_FRAMES;
            if ($key === $x) {
                $dailyTimeFrames = $scheduleService->getDailyGuardTimeFrames(
                    $schedule,
                    self::DAILY_TIME_FRAMES
                );
            }
            $totalTimeFrames = array_merge($totalTimeFrames, $dailyTimeFrames);
        }

        return $totalTimeFrames;
    }

    /**
     * Test to see if the function that initializes the
     * schedule timeline works properly.
     *
     * @return void
     */
    public function testInitializingScheduleTimeline()
    {
        $expectedDates = $this->getDatesDisplay(self::NO_OF_DAYS_DISPLAY);
        $expectedTotalTimeFrames = [];
        for ($day = 1; $day <= self::NO_OF_DAYS_DISPLAY; $day++) {
            $expectedTotalTimeFrames = array_merge($expectedTotalTimeFrames, self::DAILY_TIME_FRAMES);
        }

        //the schedule service function to be tested (initializeScheduleTimeline)
        $scheduleService = new ScheduleService();
        list(
            $actualDates,
            $actualDailyTimeFrames,
            $actualTotalTimeFrames,
            $actualDateSecurityChecker) = $scheduleService->initializeScheduleTimeline(
            self::NO_OF_DAYS_DISPLAY,
            self::MINUTES_INTERVAL
        );

        //assert if the expected and actual outputs are the same
        $this->assertTrue($actualDates === $expectedDates, self::ARRAY_ASSERTION_ERROR);
        $this->assertTrue($actualDailyTimeFrames === self::DAILY_TIME_FRAMES, self::ARRAY_ASSERTION_ERROR);
        $this->assertTrue($actualTotalTimeFrames === $expectedTotalTimeFrames, self::ARRAY_ASSERTION_ERROR);
        $this->assertTrue($actualDateSecurityChecker === self::DATE_SECURITY_CHECKER, self::ARRAY_ASSERTION_ERROR);
    }

    /**
     * Test to see if the function that gets the individual
     * guard schedule works properly.
     *
     * @return void
     */
    public function testGettingIndividualGuardSchedule()
    {
        $schedule = factory(Schedule::class)->create([
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '07:30:00',
            'end_time' => '12:00:00',
        ]);
        //set the time frames that are between the guard schedule
        $expectedDailyTimeFrames = self::DAILY_TIME_FRAMES;
        $expectedDailyTimeFrames[15] = true;
        $expectedDailyTimeFrames[16] = true;
        $expectedDailyTimeFrames[17] = true;
        $expectedDailyTimeFrames[18] = true;
        $expectedDailyTimeFrames[19] = true;
        $expectedDailyTimeFrames[20] = true;
        $expectedDailyTimeFrames[21] = true;
        $expectedDailyTimeFrames[22] = true;
        $expectedDailyTimeFrames[23] = true;

        //the schedule service function to be tested (getDailyGuardTimeFrames)
        $scheduleService = new ScheduleService();
        $actualDailyTimeFrames = $scheduleService->getDailyGuardTimeFrames(
            $schedule,
            self::DAILY_TIME_FRAMES
        );

        //assert if the expected and actual output are the same
        $this->assertTrue($actualDailyTimeFrames === $expectedDailyTimeFrames, self::ARRAY_ASSERTION_ERROR);
    }

    /**
     * Test to see if the function that constructs all the guards schedule
     * timeline works properly.
     *
     * @return void
     */
    public function testGettingAllGuardsScheduleTimeline()
    {
        $numberOfGuards = 2;
        $guards = factory(Guard::class, $numberOfGuards)->create();
        $dates = $this->getDatesDisplay(self::NO_OF_DAYS_DISPLAY);
        $times = [
            [
                'start_time' => '07:00:00',
                'end_time' => '16:00:00',
            ],
            [
                'start_time' => '09:00:00',
                'end_time' => '17:30:00',
            ],
        ];
        $expectedDateSecurityChecker = self::DATE_SECURITY_CHECKER;
        $expectedGuardSchedules = [];
        //construct the expected array data based on the given guards and their schedules
        for ($x = 0; $x < $numberOfGuards; $x++) {
            $schedule = factory(Schedule::class)->create([
                'guard_id' => $guards[$x]->id,
                'date' => $dates[$x],
                'start_time' => $times[$x]['start_time'],
                'end_time' => $times[$x]['end_time'],
            ]);
            $expectedGuardSchedules[$x] = [
                'id' => $guards[$x]->id,
                'name' => $guards[$x]->name,
                'color_indicator' => $guards[$x]->color_indicator,
                'schedules' => $this->setIndividualTotalTimeFrames($schedule, $x),
            ];
            $expectedDateSecurityChecker[$x] = true;
        }

        //the schedule service function to be tested (getGuardScheduleTimeline)
        $scheduleService = new ScheduleService();
        list ($actualGuardSchedules, $actualDateSecurityChecker) = $scheduleService->getGuardScheduleTimeline(
            $guards,
            $dates,
            self::DAILY_TIME_FRAMES,
            self::DATE_SECURITY_CHECKER
        );

        //assert if the expected and actual outputs are the same
        $this->assertTrue($actualDateSecurityChecker === $expectedDateSecurityChecker, self::ARRAY_ASSERTION_ERROR);
        $this->assertTrue($actualGuardSchedules === $expectedGuardSchedules, self::ARRAY_ASSERTION_ERROR);
    }
}
