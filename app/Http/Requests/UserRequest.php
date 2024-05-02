<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => request()->route('user')
                ? 'required|email|max:255|unique:users,email,NULL,' . request()->route('user') . ',deleted_at,NULL'
                : 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ];
    }
}
