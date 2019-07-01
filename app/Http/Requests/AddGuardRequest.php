<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddGuardRequest extends FormRequest
{
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
        return [
            'name' => 'required|unique:guards',
            'color_indicator' => 'required|unique:guards',
        ];
    }

}
