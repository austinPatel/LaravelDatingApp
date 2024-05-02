<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
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
        $id = Auth::user()->id;
        return [
            'ndis_number' => 'min:9|regex:/\b([4-5]\d{8})\b/u|unique:users,ndis_number,'.$id.',id,deleted_at,NULL',
            'term_conditions' => 'integer|in:1',
        ];
    }
}
