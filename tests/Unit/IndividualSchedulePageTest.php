<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Guard;
use App\Models\Schedule;
use Carbon\Carbon;

class IndividualSchedulePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to see the individual guard schedule page text content.
     *
     * @return void
     */
    public function testShowAddedGuardSchedule()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $guard = factory(Guard::class)->create();
        factory(Schedule::class)->create([
            'guard_id' => $guard->id,
            'date' => $tomorrow,
            'start_time' => '07:30',
            'end_time' => '15:00',
        ]);

        $this->get('/schedule/'.$guard->id)
            ->assertStatus(200)
            ->assertSeeText('Security Guard Schedules')
            ->assertSeeText($guard->name)
            ->assertSeeText($tomorrow);
    }
}
