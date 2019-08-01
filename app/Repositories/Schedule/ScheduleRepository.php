<?php

namespace App\Repositories\Schedule;

use App\Models\Schedule;
use App\Exceptions\ScheduleDeleteErrorException;
use Exception;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    protected $model;

    /**
     * Schedule Repository constructor.
     *
     * @param Schedule $schedule
     */
    public function __construct(Schedule $schedule)
    {
        $this->model = $schedule;
    }

    /**
     * Create schedule.
     *
     * @param array $data
     * @return Schedule
     */
    public function createSchedule(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Delete schedule by guard id and date.
     *
     * @param int $guardId
     * @param string $date
     * @return bool|integer 1 or 0
     * @throws ScheduleDeleteErrorException
     */
    public function deleteScheduleByGuardAndDate(int $guardId, string $date)
    {
        try {
            return $this->model->whereGuardId($guardId)
                ->where('date', $date)
                ->delete();
        } catch (Exception $e) {
            throw new ScheduleDeleteErrorException($e);
        }
    }
}
