<?php

namespace App\Repositories\Guard;

use App\Models\Guard;
use App\Exceptions\GuardDeleteErrorException;
use Exception;

class GuardRepository implements GuardRepositoryInterface
{
    protected $model;

    /**
     * Guard Repository constructor.
     *
     * @param Guard $guard
     */
    public function __construct(Guard $guard)
    {
        $this->model = $guard;
    }

    /**
     * Get all guards.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Create guard.
     *
     * @param array $data
     * @return Guard
     */
    public function createGuard(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Find guard by id.
     *
     * @param int $id
     * @return Guard
     */
    public function findGuardById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find guard by id with schedule relationships.
     *
     * @param int $id
     * @return Guard
     */
    public function findGuardByIdWithSchedules(int $id)
    {
        return $this->model->whereId($id)
            ->with(['schedules' => function ($query) {
                $query->orderBy('date', 'ASC');
            }])
            ->firstOrFail();
    }

    /**
     * Delete guard by id.
     *
     * @param int $id
     * @return bool
     * @throws GuardDeleteErrorException
     */
    public function deleteGuardById(int $id)
    {
        try {
            $this->model = $this->findGuardById($id);
            $this->model->schedules()->delete();

            return $this->model->delete();
        } catch (Exception $e) {
            throw new GuardDeleteErrorException($e);
        }
    }

    /**
     * Get all guards with schedule relationships filtered by date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGuardsWithScheduleRange(string $startDate, string $endDate)
    {
        return $this->model->with(['schedules' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'ASC');
            }])
            ->get();
    }
}
