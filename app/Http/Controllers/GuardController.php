<?php

namespace App\Http\Controllers;

use App\Models\Guard;
use App\Http\Requests\AddGuardRequest;
use App\Http\Requests\DeleteGuardRequest;

class GuardController extends Controller
{
    /**
     * Display the guard management page.
     *
     * @throws \Throwable
     */
    public function index()
    {
        $guards = Guard::all();

        return view('guard.index', [
            'guards' => $guards,
        ])->render();
    }

    /**
     * Store a newly created guard.
     *
     * @param AddGuardRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AddGuardRequest $request)
    {
        $schedule = new Guard([
            'name' => $request->get('name'),
            'color_indicator' => $request->get('color_indicator'),
        ]);
        $schedule->save();

        return redirect()
            ->route('guard-manage')
            ->with('save_success', 'Guard successfully added.');
    }

    /**
     * Delete a guard and all his schedules.
     *
     * @param DeleteGuardRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(DeleteGuardRequest $request)
    {
        $guard = Guard::find($request->get('guard_id'));
        $guard->schedules()->delete();
        $guard->delete();

        return redirect()
            ->route('guard-manage')
            ->with('delete_success', 'Guard successfully deleted.');
    }

}
