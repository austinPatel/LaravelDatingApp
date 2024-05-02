@extends('adminlte::page')

@section('title', 'Profile')

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
                <h3 class="card-title">Profile</h3>
            </div>

            {!! Form::open(array('route' => ['admin.profileUpdate', Auth::user()->id],'method'=>'POST')) !!}
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name">{{ Form::label('First name') }}</label>
                        {!! Form::text('first_name', old('first_name') ? old('first_name') : Auth::user()->first_name, array('placeholder' => 'First name', 'class' => 'form-control')) !!}

                        @error('first_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">{{ Form::label('Last name') }}</label>
                        {!! Form::text('last_name', old('last_name') ? old('last_name') : Auth::user()->last_name, array('placeholder' => 'Last name', 'class' => 'form-control')) !!}

                        @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">{{ Form::label('Email') }}</label>
                        {!! Form::email('email', old('email') ? old('email') : Auth::user()->email, array('placeholder' => 'Email', 'class' => 'form-control')) !!}

                        @error('email')
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