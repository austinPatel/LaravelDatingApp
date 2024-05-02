@extends('adminlte::page')

@section('title', 'Users')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{asset('jquery-ui/jquery-ui.min.css')}}">
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"> -->

@stop

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
                <h3 class="card-title">User edit</h3>
            </div>

            {!! Form::open(array('route' => ['admin.users.update', $user->id],'method'=>'PATCH')) !!}
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name">{{ Form::label('First name') }}</label>
                        {!! Form::text('first_name', old('first_name') ? old('first_name') : $user->first_name, array('placeholder' => 'First name', 'class' => 'form-control')) !!}

                        @error('first_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">{{ Form::label('Last name') }}</label>
                        {!! Form::text('last_name', old('last_name') ? old('last_name') : $user->last_name, array('placeholder' => 'Last name', 'class' => 'form-control')) !!}

                        @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="birthdate">{{ Form::label('Date of birth') }}</label>
                        <!-- {!! Form::text('birthdate', date('m/d/Y', strtotime(old('birthdate') ? old('birthdate') : $user->birthdate)), array('placeholder' => 'Date of birth', 'class' => 'form-control')) !!} -->
                        <input type="text" name="birthdate" class="form-control" value="{{ $user->birthdate ? date('m/d/Y',strtotime($user->birthdate)) : ''}}" autocomplete="off"/>

                        @error('birthdate')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="states">{{ Form::label('State') }}</label>
                        {{ Form::select('states', $stateList, $userState->id ?? 'null', array('class'=>'form-control', 'placeholder'=>'Please select ...','id'=>'user_state')) }}
                        @error('states')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="suburb">{{ Form::label('Suburb') }}</label>
                        <input type="text" class="form-control" placeholder="Please select suburb" name="user_suburb" id="user_suburb" value="{{$userSuburb->suburb_name ?? null}}">
                        <input type="hidden" name="suburb" id="suburb" value="{{$userSuburb->id ?? null}}">
                        <!-- <span id="error_service_provider" class="error invalid-feedback"></span> -->
                        
                        <!-- {{ Form::select('suburb', $suburbList, $userSuburb->id ?? 'null', array('class'=>'form-control', 'placeholder'=>'Please select ...')) }} -->
                        @error('suburb')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">{{ Form::label('Status') }}</label>
                        {{ Form::select('status', $statusList, $user->status, array('class'=>'form-control', 'placeholder'=>'Please select ...')) }}

                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(count($user->userSubscription) > 0 )
                    <div class="form-group">
                        <label for="subscription_status">{{ Form::label('Subscription Status') }}</label>
                        {{ Form::select('subscription_status', $subscriptionStatusList, count($user->userSubscription) > 0  ? $user->userSubscription[0]['subscription_status'] : '', array('class'=>'form-control', 'placeholder'=>'Please select ...')) }}

                        @error('subscription_status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="payment_status">{{ Form::label('Payment Status') }}</label>
                        {{ Form::select('payment_status', $paymentStatus, count($user->userSubscription) > 0  ? $user->userSubscription[0]['payment_status'] : '', array('class'=>'form-control', 'placeholder'=>'Please select ...')) }}

                        @error('payment_status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="payment_date">{{ Form::label('Payment Date') }}</label>
                        <!-- {!! Form::text('payment_date', date('m/d/Y', strtotime(old('payment_date') ? old('payment_date') : $user->userSubscription[0]->payment_date)), array('placeholder' => 'Payment Date', 'class' => 'form-control')) !!} -->
                        <input type="text" name="payment_date" class="form-control" value="{{ $user->userSubscription[0]->payment_date ? date('m/d/Y',strtotime($user->userSubscription[0]->payment_date)) : ''}}" autocomplete="off"/>
                        @error('payment_date')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif()
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

@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script>
<script>
    $(function() {
        $('input[name="birthdate"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10) - 18,
            drops: "up",
            locale: {
                format: 'MM/DD/YYYY'
            }

        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
        $('input[name="payment_date"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            maxYear: parseInt(moment().format('YYYY')),
            drops: "up",
            locale: {
                format: 'MM/DD/YYYY'
            }

        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });

		$("#user_suburb").autocomplete({
            minLength: 1,
            delay : 400,
            source: function(request, response) {
                var state_id=$("#user_state").val();
                var search_text= $("#user_suburb").val();
            	$.ajax({
	                url: "{{route('location.getSuburb')}}",
	                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	                data: {
	                    state_id : state_id,
                        search_text : search_text
	                },
	                dataType: "json",
	                beforeSend  : function () { /* showLoader(); */ },
	                complete: function () { /* hideLoader(); */ },
	                success:function(data) {
	                    response(data);
	                }
	            });
            },
            select:  function(e, ui)
            {
                var getId = ui.item.id;
                $("#suburb").val(getId);
            }

        });
        

    });
</script>
@stop