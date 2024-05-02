<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionPlanRepository;
use App\Http\Requests\SubscriptionAddUpdatePlanRequest;
use Validator;

class SubscriptionPlanController extends Controller
{

    public $subscriptionPlanRepository;
    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepository)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('admin.subscriptionPlans');
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
        try {

            $planList = $this->subscriptionPlanRepository->getPlanList();
            return view('admin.subscriptionPlanCreate', compact('planList'));
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionAddUpdatePlanRequest $request)
    {
        try {
            $input = $request->all();

            $this->subscriptionPlanRepository->addSubscriptionPlan($input);

            Session::flash('message', 'Subscription plan updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('admin.subscriptionPlans.index');
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
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
            $planStatus = SubscriptionPlan::PLAN_STATUS;
            $subscriptionPlan = $this->subscriptionPlanRepository->findSubscriptionPlanById($id);
            return view('admin.subscriptionPlanView', compact('subscriptionPlan', 'planStatus'));
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
            $planList = $this->subscriptionPlanRepository->getPlanList();
            $subscriptionPlan = $this->subscriptionPlanRepository->findSubscriptionPlanById($id);
            return view('admin.subscriptionPlanEdit', compact('subscriptionPlan', 'planList'));
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
    public function update(SubscriptionAddUpdatePlanRequest $request, $id)
    {
        try {
            $input = $request->all();

            $this->subscriptionPlanRepository->updateSubscriptionPlan($input, $id);

            Session::flash('message', 'Subscription plan updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('admin.subscriptionPlans.index');
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
            $this->subscriptionPlanRepository->deleteSubscriptionPlan($id);
            return true;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getAllSubscriptionPlans(Request $request)
    {
        $data = $this->subscriptionPlanRepository->getAllSubscriptionPlans($request);
        echo json_encode($data);
    }
}
