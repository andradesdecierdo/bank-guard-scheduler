<?php

namespace App\Repositories;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Guard\GuardRepositoryInterface;
use App\Repositories\Guard\GuardRepository;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            ScheduleRepositoryInterface::class,
            ScheduleRepository::class
        );

        $this->app->bind(
            GuardRepositoryInterface::class,
            GuardRepository::class
        );
    }
}
