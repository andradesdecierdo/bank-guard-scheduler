<?php

namespace App\Http\Controllers;

use App\Models\Guard;
use App\Models\Schedule;
use App\Http\Requests\AddScheduleRequest;
use App\Http\Requests\DeleteScheduleRequest;
use App\Services\ScheduleService;

class ScheduleController extends Controller
{
    protected $scheduleService;

    /**
     * Initialize the service used by the controller.
     * The service accepts data to process and returns the needed data.
     *
     * ScheduleController constructor.
     * @param ScheduleService $scheduleService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
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
        $guards = Guard::query()
            ->with(['schedules' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'ASC');
            }])
            ->get();

        list(
            $guardSchedules,
            $dateSecurityChecker) = $this->scheduleService->getGuardScheduleTimeline(
                $guards,
                $dates,
                $dailyTimeFrames,
                $dateSecurityChecker
            );

        return view('schedule.index', [
            'guards' => $guards,
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
        $schedule = new Schedule([
            'guard_id' => $request->get('guard_id'),
            'date' => $request->get('date'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
        ]);
        $schedule->save();

        return redirect()
            ->route('manage')
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
        Schedule::query()
            ->whereGuardId($request->get('guard_id'))
            ->where('date', $request->get('date'))
            ->delete();

        return redirect()
            ->route('manage')
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

        $guard = Guard::query()
            ->whereId($id)
            ->with(['schedules' => function ($query) {
                $query->orderBy('date', 'ASC');
            }])
            ->firstOrFail();

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
            'guard' => $guard,
            'dates' => $dates,
            'dailyTimeFrames' => $dailyTimeFrames,
            'guardSchedules' => $guardSchedules,
        ])->render();
    }

}
