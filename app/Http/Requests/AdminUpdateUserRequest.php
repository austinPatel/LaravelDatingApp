<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $date = Carbon::now();
        $before = $date->subYears(18)->format('Y-m-d');

        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'birthdate' => 'required|date|before:' . $before,
            'status' => 'required'
        ];
    }
}
