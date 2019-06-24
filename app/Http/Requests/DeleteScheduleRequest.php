<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteScheduleRequest extends FormRequest
{
    /**
     * Set the error bag to determine where the request errors are coming from.
     *
     * @var string
     */
    protected $errorBag = 'delete';

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
            'date' => ['required', 'date',
                // Check if the guard has no schedule on the input date to delete.
                function ($attribute, $value, $fail) use ($guardId) {
                    $exists = Schedule::query()
                        ->whereGuardId($guardId)
                        ->where('date', $value)
                        ->exists();

                    if (!$exists) {
                        $fail("No schedule found to be deleted.");
                    }
                }]
        ];
    }

}
