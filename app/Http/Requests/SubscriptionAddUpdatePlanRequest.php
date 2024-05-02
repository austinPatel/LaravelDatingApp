<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubscriptionAddUpdatePlanRequest extends FormRequest
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
        $planId = $this->request->get('plan_type');
        return [
            'plan_name' => $this->route('subscriptionPlan')
                ? [
                    'required',
                    Rule::unique('subscription_plans')->ignore($this->route('subscriptionPlan'))->whereNull('deleted_at')->where('plan_type', $planId)
                ]
                : 'required|max:80|unique:subscription_plans,plan_name,NULL,null,deleted_at,NULL', 'plan_type' . $planId,
            'plan_type' => 'required',
            'amount' => 'required|numeric|gt:0',
        ];
    }
}
