<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateUserRequest;
use App\Models\QuestionOption;
use App\Models\ReportUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Repositories\Admin\UserRepository;
use App\Repositories\UserRepository as UserApiRepository;
use Exception;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Http\Resources\UserLocationResource;
use App\Models\Locations;
use App\Models\State;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Repositories\LocationRepository;
use App\Repositories\SubscriptionPlanRepository;
use DateTime;


class UserController extends Controller
{
    public $userRepository;
    public $userApiRepository;
    public $subscriptionPlanRepository;
    public $locationRepository;
    public function __construct(UserRepository $userRepository, UserApiRepository $userApiRepository, SubscriptionPlanRepository $subscriptionPlanRepository)
    {
        $this->userRepository = $userRepository;
        $this->userApiRepository = $userApiRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->locationRepository = new LocationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('admin.users');
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $user = $this->userApiRepository->getUserSubscriptionDetails($id);
            
            $userState = $userSuburb ='';
            if(isset($user->userLocation) && !empty($user->userLocation)){
                $userState = State::find($user->userLocation->state_id);
                $userSuburb= $this->locationRepository->getSuburbById($user->userLocation->suburb_id);
            }
            $answer_id = count($user->answers) > 0 ? $user->answers[0]->answer_id : null;
            $userGender = $answer_id != null ? QuestionOption::where('id', $answer_id)->first()->title : null;

            return view('admin.userView', compact('user', 'userGender','userState','userSuburb'));
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $user = $this->userApiRepository->getUserSubscriptionDetails($id);
            $statusList = $this->userRepository->getUserStatusList();
            $subscriptionStatusList = $this->subscriptionPlanRepository->getSubscriptionStatusList();
            $paymentStatus = $this->subscriptionPlanRepository->getPaymentStatusList();
            $stateList = $this->locationRepository->getAllStates();
            $suburbList = Locations::orderBy('suburb_name','asc')->pluck('suburb_name','id');
            $userState = $userSuburb = null;
            if(isset($user->userLocation) && !empty($user->userLocation)){
                $userState = State::find($user->userLocation->state_id);
                $userSuburb= $this->locationRepository->getSuburbById($user->userLocation->suburb_id);
                // $userSuburb= Locations::find($user->userLocation->suburb_id);
                // $userSuburb= $user_Suburb->toarray();    
            }

            return view('admin.userEdit', compact('user','userState','userSuburb','suburbList','statusList', 'subscriptionStatusList', 'paymentStatus','stateList'));
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUpdateUserRequest $request, $id)
    {
        try {
            $input = $request->all();
            $input['birthdate'] = Carbon::createFromFormat('m/d/Y', $input['birthdate'])->format('Y-m-d');
            if(isset($input['payment_date'])){
                $input['payment_date'] = Carbon::createFromFormat('m/d/Y', $input['payment_date'])->format('Y-m-d');
            }
            $this->userRepository->updateUser($input, $id);

            Session::flash('message', 'User updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('admin.users.index');
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->deleteUser($id);
            return true;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getAllUsers(Request $request)
    {
        $users = $this->userRepository->getAllUsers($request);
        echo json_encode($users);
    }

    public function reportUsers()
    {
        try {
            return view('admin.reportUsers');
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function getReportUsers(Request $request)
    {
        $reportUsers = $this->userRepository->getReportUsers($request);
        echo json_encode($reportUsers);
    }

    public function deleteReportUser(Request $request)
    {
        try {
            $this->userRepository->deleteReportUser($request->id);
            return true;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function exportUsers(Request $request)
    {
        try {
            $exportFileName = "Users-" . Carbon::now();
            return Excel::download(new UsersExport, $exportFileName . ".csv");
        } catch (\Throwable $e) {
            \Log::debug($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!Please try again.');
        }
    }
}
