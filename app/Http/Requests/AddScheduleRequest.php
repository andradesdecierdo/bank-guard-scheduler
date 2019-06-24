<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AddScheduleRequest extends FormRequest
{
    const MIN_HOURS = 3.5;
    const MAX_HOURS = 12;

    /**
     * Set the error bag to determine where the request errors are coming from.
     *
     * @var string
     */
    protected $errorBag = 'add';

    public function authorize()
    {
        return true;
    }

    /**
     * Set the validation rules.
     */
    public function rules()
    {
        $guardId = $this->get('guard_id');

        return [
            'guard_id' => 'required|exists:guards,id',
            'date' => ['required', 'date', 'after:today',
                // Check if the guard has already a schedule on the input date.
                function ($attribute, $value, $fail) use ($guardId) {
                    $exists = Schedule::query()
                        ->whereGuardId($guardId)
                        ->where('date', $value)
                        ->exists();

                    if ($exists) {
                        $fail("The selected schedule already exists");
                    }
            }],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }

    /**
     * Add additional validation.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->start_time && $this->end_time) {
                $this->validateDuration($validator, $this->start_time, $this->end_time);
            }
        });
    }

    /**
     * Modify the error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'end_time.after'  => 'The end time must be later than start time!',
        ];
    }

    /**
     * Validate the start time and end time.
     * The difference between the start time and end time should not be less than 3.5 hours
     * and not greater than 12 hours.
     *
     * @param $validator
     * @param $start
     * @param $end
     */
    private function validateDuration($validator, $start, $end) {
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);
        $diff = $startTime->diffInMinutes($endTime);
        if ($diff < (60 * self::MIN_HOURS)) {
            $validator->errors()->add('invalid_range', 'A security guard must work not less than 3.5 hours.');
        }
        if ($diff > (60 * self::MAX_HOURS)) {
            $validator->errors()->add('invalid_range', 'Please do not overwork your security guard. The maximum working hours is 12.');
        }
    }

}
