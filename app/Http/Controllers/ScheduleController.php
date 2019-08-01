<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddScheduleRequest;
use App\Http\Requests\DeleteScheduleRequest;
use App\Services\ScheduleService;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\Guard\GuardRepositoryInterface;

class ScheduleController extends Controller
{
    protected $scheduleService;
    protected $scheduleRepository;
    protected $guardRepository;

    /**
     * Initialize the service and repositories used by the controller.
     * The service accepts data to process and returns the needed data.
     * The repository handles database processes
     *
     * ScheduleController constructor.
     * @param ScheduleService $scheduleService
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param GuardRepositoryInterface $guardRepository
     */
    public function __construct(
        ScheduleService $scheduleService,
        ScheduleRepositoryInterface $scheduleRepository,
        GuardRepositoryInterface $guardRepository
    ) {
        $this->scheduleService = $scheduleService;
        $this->scheduleRepository = $scheduleRepository;
        $this->guardRepository = $guardRepository;
    }

    /**
     * Display the scheduler page.
     *
     * @throws \Throwable
     */
    public function index()
    {
        list(
            $dates,
            $dailyTimeFrames,
            $totalTimeFrames,
            $dateSecurityChecker) = $this->scheduleService->initializeScheduleTimeline(3, 30);

        $startDate = $dates[0];
        $endDate = collect($dates)->last();
        $guards = $this->guardRepository->getGuardsWithScheduleRange(
            $startDate,
            $endDate
        );

        list(
            $guardSchedules,
            $dateSecurityChecker) = $this->scheduleService->getGuardScheduleTimeline(
                $guards,
                $dates,
                $dailyTimeFrames,
                $dateSecurityChecker
            );

        return view('schedule.index', [
            'guards' => $guards->pluck('name', 'id'),
            'dates' => $dates,
            'dailyTimeFrameCount' => count($dailyTimeFrames),
            'totalTimeFrames' => $totalTimeFrames,
            'guardSchedules' => $guardSchedules,
            'dateSecurityChecker' => $dateSecurityChecker,
        ])->render();
    }

    /**
     * Store a newly created guard schedule.
     *
     * @param AddScheduleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddScheduleRequest $request)
    {
        $this->scheduleRepository->createSchedule([
            'guard_id' => $request->get('guard_id'),
            'date' => $request->get('date'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
        ]);

        return redirect()
            ->route('schedule-manage')
            ->with('save_success', 'Schedule successfully added.');
    }

    /**
     * Delete a guard schedule.
     *
     * @param DeleteScheduleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(DeleteScheduleRequest $request)
    {
        $this->scheduleRepository->deleteScheduleByGuardAndDate(
            $request->get('guard_id'),
            $request->get('date')
        );

        return redirect()
            ->route('schedule-manage')
            ->with('delete_success', 'Schedule successfully deleted.');
    }

    /**
     * Show all the guard's schedules.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function show($id)
    {
        list(
            $dates,
            $dailyTimeFrames) = $this->scheduleService->initializeScheduleTimeline(3, 30);

        $guard = $this->guardRepository->findGuardByIdWithSchedules($id);

        $guardSchedules = [];
        foreach ($guard->schedules as $schedule) {
            $dailySchedule['day'] = $schedule->date;
            $dailySchedule['schedules'] = $this->scheduleService->getDailyGuardTimeFrames(
                $schedule,
                $dailyTimeFrames
            );
            $guardSchedules[] = $dailySchedule;
        }

        return view('schedule.show', [
            'guardName' => $guard->name,
            'colorIndicator' => $guard->color_indicator,
            'dates' => $dates,
            'dailyTimeFrames' => $dailyTimeFrames,
            'guardSchedules' => $guardSchedules,
        ])->render();
    }

}
