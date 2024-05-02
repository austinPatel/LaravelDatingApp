@extends('layouts.master')
@section('content')
<div class="login-top ">
    <div class="login-wrapper">
        <div class="login-box mt-3">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="heading">
                    <h3>{{ "Reset Password" }}</h3>
                </div>

                <div class="form-wrapper carry-form">
                    <input type="hidden" name="token" value="{{ $data['token'] }}">

                    @if (Session::has('success'))
                    <div class="alert alert-info">{{ Session::get('success') }}</div>
                    @endif

                    @if($errors->any())
                    <h4>{{$errors->first()}}</h4>
                    @endif

                    <div class="form-group">
                        <label for="email">{{ "Email" }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $data['email'] ?? old('email') }}" autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ "Password" }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                        <!-- <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                            </div>
                        </div> -->
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password-confirm">{{ "Confirm Password" }}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                    </div>

                    <div class="form-group ">
                        <button type="submit" class="form-control btn btn-primary d-block">
                            {{"Submit"}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection