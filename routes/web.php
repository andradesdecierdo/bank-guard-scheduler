<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Schedule
Route::get('/schedule', "ScheduleController@index")->name('schedule-manage');
Route::post('/schedule', "ScheduleController@store")->name('schedule-add');
Route::delete('/schedule', "ScheduleController@delete")->name('schedule-delete');
Route::get('/schedule/{id}', "ScheduleController@show")->name('schedule-show');
// Guard
Route::get('/guard', "GuardController@index")->name('guard-manage');
Route::post('/guard', "GuardController@store")->name('guard-add');
Route::delete('/guard', "GuardController@delete")->name('guard-delete');
