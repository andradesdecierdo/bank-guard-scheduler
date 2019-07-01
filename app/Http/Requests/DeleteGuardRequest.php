<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteGuardRequest extends FormRequest
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
        return [
            'guard_id' => 'required|exists:guards,id',
        ];
    }

}
