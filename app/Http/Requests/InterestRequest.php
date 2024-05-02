<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterestRequest extends FormRequest
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
        return [
            'interests' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'interests.required' => 'You have to select atleast 2 interest.'
        ];
    }
}
