@extends('adminlte::page')

@section('title', 'Change password')

@section('content_header')
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Change password</h3>
            </div>

            {!! Form::open(array('route' => 'admin.changePasswordStore','method'=>'POST')) !!}
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="current_password">{{ Form::label('Current password') }}</label>
                        {!! Form::password('current_password', array('placeholder' => 'Current Password','class' => 'form-control', 'id' => 'current_password')) !!}

                        @error('current_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">{{ Form::label('New password') }}</label>
                        {!! Form::password('new_password', array('placeholder' => 'New Password','class' => 'form-control')) !!}

                        @error('new_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">{{ Form::label('Confirm password') }}</label>
                        {!! Form::password('confirm_password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}

                        @error('confirm_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop