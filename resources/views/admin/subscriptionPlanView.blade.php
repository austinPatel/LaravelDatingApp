@extends('adminlte::page')

@section('title', 'Subscription Plans')

@section('content_header')
<h1>Subscription Plan details</h1>
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<div class="col-md-6">
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
            </div>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <b>Plan Name</b> <span class="float-right">{{ $subscriptionPlan->plan_name }}</span>
                </li>
                <li class="list-group-item">
                    <b>Plan Type</b> <span class="float-right">{{ $planStatus[$subscriptionPlan->plan_type] }}</span>
                </li>
                <li class="list-group-item">
                    <b>Amount ($)</b> <span class="float-right">{{ $subscriptionPlan->amount }}</span>
                </li>
                <li class="list-group-item">
                    <b>Description</b> <span class="float-right">{{ $subscriptionPlan->description }}</span>
                </li>
                <li class="list-group-item">
                    <b>Created At</b> <span class="float-right">{{ convertToViewDateOnly($subscriptionPlan->created_at) }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@stop