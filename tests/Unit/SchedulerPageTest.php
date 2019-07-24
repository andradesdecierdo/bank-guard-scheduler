<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Guard;
use App\Models\Schedule;
use Carbon\Carbon;

class SchedulerPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get schedule dates to be displayed (next 3 days).
     *
     * @return array $dates
     */
    private function getScheduleTimeline()
    {
        $startDay = Carbon::tomorrow();
        $endDay = $startDay->copy()->addDay(3);
        $dates = [];

        for ($day = $startDay; $day->lt($endDay); $day->addDay(1)) {
            $dates[] = $day->toDateString();
        }

        return $dates;
    }

    /**
     * Send add schedule request.
     *
     * @param $guardId
     * @param $date
     * @param $startTime
     * @param $endTime
     *
     * @return \Illuminate\Foundation\Testing\TestResponse $response
     */
    private function sendAddScheduleRequest($guardId, $date, $startTime, $endTime)
    {
        $response = $this->call( 'POST','/schedule', [
            'guard_id' => $guardId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * Send delete schedule request.
     *
     * @param $guardId
     * @param $date
     *
     * @return \Illuminate\Foundation\Testing\TestResponse $response
     */
    private function sendDeleteScheduleRequest($guardId, $date)
    {
        $response = $this->call( 'DELETE','/schedule', [
            'guard_id' => $guardId,
            'date' => $date,
        ]);
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * Test to see the scheduler page text content.
     *
     * @return void
     */
    public function testPageContent()
    {
        $response = $this->get('/schedule')
            ->assertStatus(200)
            //header
            ->assertSeeText('Bank Guard Scheduler')
            ->assertSeeText('Schedules')
            ->assertSeeText('Guards')
            //roster security guard form
            ->assertSeeText('Roster Security Guard')
            ->assertSeeText('Guard:')
            ->assertSeeText('Date:')
            ->assertSeeText('Start Time:')
            ->assertSeeText('End Time:')
            ->assertSeeText('Submit')
            //remove security guard roster form
            ->assertSeeText('Remove Security Guard Roster')
            ->assertSeeText('Guard:')
            ->assertSeeText('Date:')
            ->assertSeeText('Delete')
            //bank security guard schedule table
            ->assertSeeText('Bank Security Guard Schedules');

        $dates = $this->getScheduleTimeline();
        foreach ($dates as $date) {
            $response->assertSeeText($date);
        }
    }

    /**
     * Test for adding a valid guard schedule.
     *
     * @return void
     */
    public function testAddGuardScheduleSuccessful()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $startTime = '06:30';
        $endTime = '15:00';

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendAddScheduleRequest(
            $guard->id,
            $tomorrow,
            $startTime,
            $endTime
        );
        $response->assertStatus(200)
            //success message
            ->assertSeeText('Schedule successfully added.');
    }

    /**
     * Test validation for adding an existing guard schedule.
     *
     * @return void
     */
    public function testAddExistingGuardSchedule()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $startTime = '06:30';
        $endTime = '15:00';
        factory(Schedule::class)->create([
            'guard_id' => $guard->id,
            'date' => $tomorrow,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendAddScheduleRequest(
            $guard->id,
            $tomorrow,
            $startTime,
            $endTime
        );
        $response->assertStatus(200)
            //error message
            ->assertSeeText('The selected schedule already exists');
    }

    /**
     * Test the validation for submitting schedule with less than the minimum working hours.
     *
     * @return void
     */
    public function testBelowMinimumHoursSchedule()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $startTime = '06:30';
        $endTime = '07:00';

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendAddScheduleRequest(
            $guard->id,
            $tomorrow,
            $startTime,
            $endTime
        );
        $response->assertStatus(200)
            //error message
            ->assertSeeText('A security guard must work not less than 3.5 hours.');
    }

    /**
     * Test the validation for submitting empty schedule form.
     *
     * @return void
     */
    public function testEmptyScheduleForm()
    {
        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendAddScheduleRequest(
            '',
            '',
            '',
            ''
        );
        $response->assertStatus(200)
            //error messages
            ->assertSeeText('The guard id field is required.')
            ->assertSeeText('The date field is required.')
            ->assertSeeText('The start time field is required.')
            ->assertSeeText('The end time field is required.');
    }

    /**
     * Test the validation for submitting schedule with more than the maximum working hours.
     *
     * @return void
     */
    public function testAboveMaximumHoursSchedule()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $startTime = '07:00';
        $endTime = '22:00';

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendAddScheduleRequest(
            $guard->id,
            $tomorrow,
            $startTime,
            $endTime
        );
        $response->assertStatus(200)
            //error message
            ->assertSeeText('Please do not overwork your security guard. The maximum working hours is 12.');
    }

    /**
     * Test for deleting a valid guard schedule.
     *
     * @return void
     */
    public function testDeleteGuardScheduleSuccessful()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $startTime = '06:30';
        $endTime = '12:00';
        factory(Schedule::class)->create([
            'guard_id' => $guard->id,
            'date' => $tomorrow,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendDeleteScheduleRequest(
            $guard->id,
            $tomorrow
        );
        $response->assertStatus(200)
            //success message
            ->assertSeeText('Schedule successfully deleted.');
    }

    /**
     * Test the validation for deleting an invalid guard schedule.
     *
     * @return void
     */
    public function testDeleteInvalidGuardSchedule()
    {
        $guard = factory(Guard::class)->create();
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = $tomorrow->copy()->addDay(1);
        $startTime = '06:30';
        $endTime = '12:00';
        factory(Schedule::class)->create([
            'guard_id' => $guard->id,
            'date' => $tomorrow->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendDeleteScheduleRequest(
            $guard->id,
            $dayAfterTomorrow->toDateString()
        );
        $response->assertStatus(200)
            //error message
            ->assertSeeText('No schedule found to be deleted.');
    }

    /**
     * Test the validation for deleting guard schedule with empty form.
     *
     * @return void
     */
    public function testDeleteEmptyGuardScheduleForm()
    {
        $this->get('/schedule')
            ->assertStatus(200);
        $response = $this->sendDeleteScheduleRequest(
            '',
            ''
        );
        $response->assertStatus(200)
            //error messages
            ->assertSeeText('The guard id field is required.')
            ->assertSeeText('The date field is required.');
    }

    /**
     * Test the redirection to individual guard schedule page.
     *
     * @return void
     */
    public function testRedirectToIndividualGuardSchedulePage()
    {
        $guard = factory(Guard::class)->create();

        $this->get('/guard')
            ->assertStatus(200);
        $this->get('/schedule/'.$guard->id)
            ->assertStatus(200)
            ->assertSeeText('Security Guard Schedules')
            ->assertSeeText($guard->name);
    }
}
