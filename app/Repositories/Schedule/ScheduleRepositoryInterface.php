<?php

namespace App\Repositories\Schedule;

interface ScheduleRepositoryInterface
{
    public function createSchedule(array $data);

    public function deleteScheduleByGuardAndDate(int $guardId, string $date);
}
