@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">

                    @if (Session::has('verifyMessage'))
                    <div class="alert alert-success" role="alert">{{ Session::get('verifyMessage') }}</div>
                    @endif

                    @if (Route::has('verification.verify'))
                    @if($verified)

                    <p><b>User email {{$user->email}} </b> is verified successfully. Please open mobile app and try to login.</p>

                    @endif
                    @endif

                    @if (Route::has('verification.resend'))
                    @if (!$hasValidSig && !$verified && !Session::has('verifyMessage'))
                    {{ "Your token is expired" }},

                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>

                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection