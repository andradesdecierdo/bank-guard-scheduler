<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\ScheduleDeleteErrorException;
use App\Models\Guard;
use App\Models\Schedule;
use App\Repositories\Schedule\ScheduleRepository;
use Carbon\Carbon;

class ScheduleRepositoryUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test for creating a schedule.
     *
     * @return void
     */
    public function testCreateScheduleSuccessful()
    {
        $guard = factory(Guard::class)->create();
        $data = [
            'guard_id' => $guard->id,
            'date' => $tomorrow = Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '15:00',
        ];

        $scheduleRepository = new ScheduleRepository(new Schedule());
        $scheduleCreated = $scheduleRepository->createSchedule($data);

        $this->assertInstanceOf(Schedule::class, $scheduleCreated);
        $this->assertEquals($data['guard_id'], $scheduleCreated->guard_id);
        $this->assertEquals($data['date'], $scheduleCreated->date);
        $this->assertEquals($data['start_time'], $scheduleCreated->start_time);
        $this->assertEquals($data['end_time'], $scheduleCreated->end_time);
    }

    /**
     * Test for deleting a schedule by guard and date.
     *
     * @return void
     * @throws ScheduleDeleteErrorException
     */
    public function testDeleteScheduleByGuardAndDateSuccessful()
    {
        $schedule = factory(Schedule::class)->create([
            'start_time' => '08:00',
            'end_time' => '16:30',
        ]);

        $scheduleRepository = new ScheduleRepository(new Schedule());
        $result = $scheduleRepository->deleteScheduleByGuardAndDate(
            $schedule->guard_id,
            $schedule->date
        );

        $this->assertTrue($result === 1);
    }

    /**
     * Test for deleting a nonexistent schedule.
     *
     * @return void
     * @throws ScheduleDeleteErrorException
     */
    public function testDeleteNonExistentScheduleFailed()
    {
        $scheduleRepository = new ScheduleRepository(new Schedule());
        $result = $scheduleRepository->deleteScheduleByGuardAndDate(
            2,
            $tomorrow = Carbon::tomorrow()->format('Y-m-d')
        );

        $this->assertTrue($result === 0);
    }

    /**
     * Test for creating a schedule with empty data to throw a query exception error.
     *
     * @return void
     */
    public function testCreateWithEmptyScheduleDataThrowExceptionError()
    {
        $this->expectException(QueryException::class);

        $scheduleRepository = new ScheduleRepository(new Schedule());
        $scheduleRepository->createSchedule([]);
    }

    /**
     * Test for deleting using invalid parameters to throw
     * a custom schedule delete exception error.
     *
     * @return void
     * @throws ScheduleDeleteErrorException
     */
    public function testDeleteWithInvalidParametersThrowExceptionError()
    {
        $this->expectException(ScheduleDeleteErrorException::class);

        $scheduleRepository = new ScheduleRepository(new Schedule());
         $scheduleRepository->deleteScheduleByGuardAndDate(
            2,
            $tomorrow = Carbon::tomorrow()->format('d-m-Y') //invalid format
        );
    }

}
