<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserSubscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Repositories\LocationRepository;
use App\Models\State;

class UsersExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\user\Collection
     */
    public function collection()
    {

        $users = User::with(['userSubscription', 'userSubscription.subscriptionPlan','userLocation'])->orderBy('id', 'asc')->get();
        $userStatus = User::USER_STATUS;
        $userSubscriptionStatus = UserSubscription::USER_SUBSCRIPTION_STATUS;
        $userPaymentStatus = UserSubscription::USER_PAYMENT_STATUS;
        $data = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $userState = $userSuburb ='';
                if(isset($user->userLocation) && !empty($user->userLocation)){
                    $userState = State::find($user->userLocation->state_id);
                    $userLocationRepository= new LocationRepository;
                    $userSuburb= $userLocationRepository->getSuburbById($user->userLocation->suburb_id);
                }

                $send_invoice ="";
                if(count($user->userSubscription) > 0){
                    $send_invoice= $user->userSubscription[0]['send_invoice'] ? "yes":"no";
                }
                
                $nestedData['name'] = $user->first_name . ' ' . $user->last_name;
                $nestedData['ndis_number'] = $user->ndis_number;
                $nestedData['mobile'] = $user->mobile;
                $nestedData['email'] = $user->email;
                $nestedData['status'] = $user->status != null ? $userStatus[$user->status] : "--";
                $nestedData['created_at'] = $user->created_at->format('d-m-Y');
                $nestedData['state']=$userState->name ?? '';
                $nestedData['suburb']=$userSuburb->suburb_name ?? '';
                $nestedData['subscription_plan'] = count($user->userSubscription) > 0 ? $user->userSubscription[0]->subscriptionPlan->plan_name : " ";
                $nestedData['subscription_status'] = count($user->userSubscription) > 0 ? $userSubscriptionStatus[$user->userSubscription[0]['subscription_status']] : " ";
                $nestedData['subscription_date'] = count($user->userSubscription) > 0  ? ($user->userSubscription[0]['created_at'])->format('d-m-Y') : " ";
                $nestedData['plan_manager_name'] = count($user->userSubscription) > 0  ? ($user->userSubscription[0]['plan_manager_name']) : " ";
                $nestedData['plan_manager_email'] = count($user->userSubscription) > 0 ? $user->userSubscription[0]['plan_manager_email'] : " ";
                $nestedData['send_invoice'] = count($user->userSubscription) > 0 ? $send_invoice : " ";
                $nestedData['payment_status'] = count($user->userSubscription) > 0 ? $userPaymentStatus[$user->userSubscription[0]['payment_status']] : " ";
                $data[] = $nestedData;
            }
        }
        return collect($data);
    }


    public function headings(): array
    {
        return [
            'Name',
            'NDIS Number',
            'Mobile',
            'Email',
            'Status',
            'Created At',
            'State',
            'Suburb',
            'Subscription Plan',
            'Subscription Status',
            'Subscription Date',
            'Plan Manger Name',
            'Plan Manger Email',
            'Send Invoice',
            'Payment Status',
        ];
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
