<?php

namespace App\Repositories\Guard;

interface GuardRepositoryInterface
{
    public function all();

    public function createGuard(array $data);

    public function findGuardById(int $id);

    public function findGuardByIdWithSchedules(int $id);

    public function deleteGuardById(int $id);

    public function getGuardsWithScheduleRange(string $startDate, string $endDate);
}
