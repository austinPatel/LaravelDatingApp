<?php

namespace App\Repositories;

use App\Jobs\SendReportEmailJob;
use App\Models\ReportUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReportUserRepository
{
    public function reportUser($request)
    {
        $data = $request->all();
        $data['to_user_id'] = $request->user_id;
        $data['user_id'] = Auth::user()->id;
        $path = '';

        if (isset($data['file'])) {
            $fileName = time() . '.' . $data['file']->extension();
            $path = $data['file']->storeAs('reportUser/user-' . $data['user_id'], $fileName, 'azure');
        }

        $data['file'] = $data['objectional_type'] == ReportUser::USER_REPORT_TYPE ? env('AZURE_STORAGE_URL') . "webdata/" . $path : $data['file_url'];

        ReportUser::create($data);

        $to_user = User::findOrFail($data['to_user_id']);

        $details['email'] = env('MAIL_FROM_ADDRESS');
        $details['from_user_name'] = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $details['to_user_name'] = $to_user->first_name . ' ' . $to_user->last_name;

        dispatch(new SendReportEmailJob($details));

        return true;
    }
}
