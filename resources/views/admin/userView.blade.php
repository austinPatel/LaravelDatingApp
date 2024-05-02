@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')

@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif
<div class="row">
    <div class="col-md-6 mt-4">
        <h3>User detail</h3>
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <!-- <img class="profile-user-img img-fluid img-circle" src="" alt="Profile picture"> -->
                </div>
                <!-- <h3 class="profile-username text-center">Nina Mcintire</h3>
            <p class="text-muted text-center">Software Engineer</p> -->
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>First name</b> <span class="float-right">{{ $user->first_name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Last name</b> <span class="float-right">{{ $user->last_name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <span class="float-right">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Mobile</b> <span class="float-right">{{ $user->mobile }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Date of birth</b> <span class="float-right">{{ \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>NDIS number</b> <span class="float-right">{{ $user->ndis_number }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>State</b> <span class="float-right">{{$userState->name ?? 'N/A'}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Suburb</b> <span class="float-right">{{$userSuburb->suburb_name ?? 'N/A'}}</span>
                    </li>

                    <li class="list-group-item">
                        <b>Age</b> <span class="float-right">{{ $user->age }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Gender</b> <span class="float-right">{{ $userGender }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Email verified</b> <span class="float-right">{{ $user->email_verified_at != null ? 'True' : 'False' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Mobile verified</b> <span class="float-right">{{ $user->isVerifiyMobile == 1 ? 'True' : 'False' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <span class="float-right">{{ $user->status != NULL ? userStatus($user->status) : "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Created at</b> <span class="float-right">{{ convertToViewDateOnly($user->created_at) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    @if(count($user->userSubscription) > 0)

    <div class="col-md-6  mt-4">
        <h3>Subscription Detail</h3>

        @foreach($user->userSubscription as $userSubscription)
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                </div>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Subscription Plan</b> <span class="float-right">{{ $userSubscription->subscriptionPlan->plan_name ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Subscription Status</b> <span class="float-right">{{ getUserSubscriptionStatus($userSubscription->subscription_status) ??  "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Subscription Date</b> <span class="float-right">{{ convertToViewDateOnly($userSubscription->subscription_date) ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Amount ($)</b> <span class="float-right">{{ $userSubscription->subscriptionPlan->amount ??  "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Payment Status</b> <span class="float-right">{{ userPaymentStatus($userSubscription->payment_status) ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Payment Date</b> <span class="float-right">{{ $userSubscription->payment_date ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Expire At</b> <span class="float-right">{{ $userSubscription->expire_at ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Plan Manager Name</b> <span class="float-right">{{ $userSubscription->plan_manager_name ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Plan Manager Email</b> <span class="float-right">{{ $userSubscription->plan_manager_email ?? "N/A" }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Send Invoice</b> <span class="float-right">{{ $userSubscription->send_invoice ? "Yes" : "No" }}</span>
                    </li>
                </ul>
            </div>
        </div>
        @endforeach()
    </div>
    @else
    <div class="col-md-6  mt-4">
        <h3>Subscription Detail</h3>
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                </div>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Subscription Plan</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Subscription Status</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Subscription Date</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Amount ($)</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Payment Status</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Payment Date</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Expire At</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Plan Manager Name</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Plan Manager Email</b> <span class="float-right">N/A</span>
                    </li>
                    <li class="list-group-item">
                        <b>Send Invoice</b> <span class="float-right">N/A</span>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    @endif()
</div>

@stop