@extends('adminlte::page')

@section('title', 'Reported users')

@section('content_header')
<h1>Reported users</h1>
@stop

@section('css')
<style>
    /* .userNameTd {
        word-break: break-all !important;
    }

    .actionBtns {
        width: 295px !important;
    } */

    .table img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .file {
        width: 200px;
        word-break: break-all;
    }
</style>
@stop

@section('content')
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif

<table id="reportUsersTable" class="display table table-bordered">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Reported By</th>
            <th>Reported To</th>
            <th>File</th>
            <th>Type</th>
            <th>Reason</th>
            <th>Channel URL</th>
            <th>Objectional Type</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@stop

@section('js')

<script>
    $(function() {
        $('#reportUsersTable').DataTable({
            "responsive": true,
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{route('admin.getReportUsers')}}",
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
                    "data": "fromUser",
                    "orderable": false
                },
                {
                    "data": "toUser",
                    "orderable": false
                },
                {
                    "data": "file",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "type",
                    "orderable": false
                },
                {
                    "data": "reason"
                },
                {
                    "data": "channelUrl",
                    "orderable": false
                },
                {
                    "data": "objectionalType",
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
                    "searchable": false
                }
            ],
            order: [
                [0, 'desc']
            ],
        });
    });

    function deleteUser(user_id) {
        $.ajax({
            url: 'deleteReportUser',
            type: 'POST',
            dataType: 'json',
            data: {
                method: 'POST',
                _token: "{{csrf_token()}}",
                id: user_id
            }
        }).always(function(data) {
            $('#reportUsersTable').DataTable().draw(true);
        });
    }
</script>
@stop