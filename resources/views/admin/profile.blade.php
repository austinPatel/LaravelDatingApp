@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
<h1>Profile</h1>
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<div class="col-md-6">
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                <!-- <img class="profile-user-img img-fluid img-circle" src="" alt="Profile picture"> -->
            </div>
            <!-- <h3 class="profile-username text-center">Nina Mcintire</h3>
            <p class="text-muted text-center">Software Engineer</p> -->
            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <b>First name</b> <span class="float-right">{{ Auth::user()->first_name }}</span>
                </li>
                <li class="list-group-item">
                    <b>Last name</b> <span class="float-right">{{ Auth::user()->last_name }}</span>
                </li>
                <li class="list-group-item">
                    <b>Email</b> <span class="float-right">{{ Auth::user()->email }}</span>
                </li>
            </ul>
            <a href="{{ route('admin.profileEdit') }}" class="btn btn-primary btn-block"><b>Edit</b></a>
        </div>
    </div>
</div>
@stop