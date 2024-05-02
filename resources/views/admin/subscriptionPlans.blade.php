@extends('adminlte::page')

@section('title', 'Subscription Plans')

@section('content_header')
<h1>Subscription Plans</h1>
@stop

@section('css')
<style>
    /* .userNameTd {
        word-break: break-all !important;
    }

    .actionBtns {
        width: 295px !important;
    } */

    .dataTables_scrollHeadInner,
    div.dataTables_scrollBody table {
        min-width: 100%;
    }
</style>
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<div class="form-group">
    <a type='button' style="margin:10px;" class='btn btn-primary' href="{{ url('admin/subscriptionPlans/create ') }}">Add</a>
</div>

<table id="subscriptionPlansTable" class="display table table-bordered">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Plan Name</th>
            <th>Plan Type</th>
            <th>Amount ($)</th>
            <th>Created At</th>
            <th class='actionBtns'>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop

@section('js')
<script>
    $(function() {
        $('#subscriptionPlansTable').DataTable({
            "responsive": true,
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{route('admin.subscriptionPlans.getAllSubscriptionPlans')}}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    _token: "{{csrf_token()}}"
                }
            },
            "columns": [{
                    "data": "id",
                },
                {
                    "data": "plan_name"
                },
                {
                    "data": "plan_type"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "created_at",
                    "searchable": false
                },
                {
                    "data": "options",
                    "orderable": false,
                    "searchable": false,
                    "width": 300
                }
            ],
            order: [
                [0, 'desc']
            ],
        });
    });

    function deleteSubscriptionPlan(id) {
        Swal.fire({
            title: "Are you sure, want to delete this plan!",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $.ajax({
                    url: 'subscriptionPlans/' + id,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        method: '_DELETE',
                        _token: "{{csrf_token()}}"
                    }
                }).always(function(data) {
                    $('#subscriptionPlansTable').DataTable().draw(true);
                });
            }
        });
    }
</script>
@stop