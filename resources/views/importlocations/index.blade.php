@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Import Locations') }}</div>

                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-error" role="alert">
                            {{session('error') }}
                        </div>
                    @endif
                    <form class="d-inline" method="POST" action="{{ route('import.locations') }}" method="POST" id="import-locations" enctype="multipart/form-data">
                        @csrf
                        <input type="file" class="form-control-uniform document_upload_input" data-fouc="" name="attachment">
                        <button type="submit" class="">{{ __('Submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
