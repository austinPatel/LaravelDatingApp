@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
<h1>Users</h1>
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

    .exportCSV {
        margin-left: 884px;
        ;
    }
</style>
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<div class="form-group">
    <a href="{{ route('admin.exportUsers') }}" class="btn btn-info btn-icon-sm exportCSV">
        <i class="la la-download"></i> Export CSV
    </a>
</div>

<table id="usersTable" class="display table table-bordered">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>State</th>
            <th>Suburb</th>
            <th>Status</th>
            <th>Subscription Status</th>
            <th>Remaining Days</th>
            <th>Payment Status</th>
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
        $('#usersTable').DataTable({
            "responsive": true,
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{route('admin.users.getAllUsers')}}",
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
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "state"
                },
                {
                    "data": "suburb"
                },
                {
                    "data": "status",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "subscription_status",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "remaning_days",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "payment_status",
                    "orderable": false,
                    "searchable": false
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

    function deleteUser(id) {

        Swal.fire({
            title: "Are you sure, want to delete this user!",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $.ajax({
                    url: 'users/' + id,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        method: '_DELETE',
                        _token: "{{csrf_token()}}"
                    }
                }).always(function(data) {
                    $('#usersTable').DataTable().draw(true);
                });

            }
        });
    }
</script>
@stop