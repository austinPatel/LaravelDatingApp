@extends('adminlte::page')

@section('title', 'Subscription Plans')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
                <h3 class="card-title">Subscription Plan Edit</h3>
            </div>

            {!! Form::open(array('route' => ['admin.subscriptionPlans.update', $subscriptionPlan->id],'method'=>'PATCH')) !!}
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="plan_name">{{ Form::label('Plan Name') }}</label>
                        {!! Form::text('plan_name', old('plan_name') ? old('plan_name') : $subscriptionPlan->plan_name, array('placeholder' => 'Plan Name', 'class' => 'form-control')) !!}

                        @error('plan_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="plan_type">{{ Form::label('Plan Type') }}</label>
                        {{ Form::select('plan_type',$planList, $subscriptionPlan->plan_type, array('class'=>'form-control', 'placeholder'=>'Please select ...')) }}

                        @error('plan_type')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="amount">{{ Form::label('Amount ($)') }}</label>
                        {!! Form::text('amount', old('amount') ? old('amount') : $subscriptionPlan->amount, array('placeholder' => 'Amount', 'class' => 'form-control', 'id'=> 'amount')) !!}

                        @error('amount')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ Form::label('Description') }}</label>
                        {!! Form::textarea('description', old('description') ? old('description') : $subscriptionPlan->description, array('placeholder' => 'Description', 'class' => 'form-control', 'rows' => 2, 'cols' => 40)) !!}

                        @error('description')
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

@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#amount').on('keydown', function(event) {
            return isNumber(event, this);
        });

        function isNumber(evt, element) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            $("#amount").attr("maxlength", "9");
            if ((charCode != 190 || $(element).val().indexOf('.') != -1) // “.” CHECK DOT, AND ONLY ONE.
                &&
                (charCode != 110 || $(element).val().indexOf('.') != -1) // “.” CHECK DOT, AND ONLY ONE.
                &&
                ((charCode < 48 && charCode != 8) ||
                    (charCode > 57 && charCode < 96) ||
                    charCode > 105))
                return false;
            return true;
        }
    });
</script>
@stop