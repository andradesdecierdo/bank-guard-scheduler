<?php

namespace App\Http\Controllers;

use App\Models\Guard;
use App\Models\Schedule;
use App\Http\Requests\AddScheduleRequest;
use App\Http\Requests\DeleteScheduleRequest;

class ScheduleController extends Controller
{
    /**
     * Display the scheduler page.
     *
     * @throws \Throwable
     */
    public function index()
    {
        $guards = Guard::all();
        return view('schedule.index', ['guards' => $guards])->render();
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

}
