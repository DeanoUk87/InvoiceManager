
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.admincomposer.title')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="content-loader">
        <form action="{{route('admincomposer.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row mb-2 htsDisplay">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <nav class="nav  justify-content-between">
                                <a class="navbar-brand">Messages Management</a>
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="submit" class="btnDelete btn btn-danger btn-sm" name="btn-delete" id="btnStatus" style="display: none">
                                            <span class="fa fa-trash"></span> @lang('app.delete')
                                        </button>
                                        <a href="{{route('admincomposer.create')}}" class="btn btn-info btn-sm" >
                                            <i class="fa fa-inbox"></i> Compose
                                        </a>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <table id="admincomposer_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                    <thead>
                                    <tr class="text-primary">
                                        <th></th>
                                        <th>
                                            <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                            <label for="checkAll" class="checklabel"></label>
                                        </th>
                                        {{--<th>@lang('main.admincomposer.field.to')</th>--}}
                                        <th>@lang('main.admincomposer.field.title')</th>
                                        <th>@lang('main.admincomposer.field.created_at')</th>
                                        <th>@lang('main.admincomposer.field.message_by')</th>
                                        <th>Attachment</th>
                                        <th>Message</th>
                                        <th>Sent Status</th>
                                        <th>@lang('app.actions')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts');
<script type="text/javascript">
    var table = $('#admincomposer_datatable').DataTable({
        processing: true,
        serverSide: true,
        "order": [[0, "desc" ]],
        "aoColumnDefs": [
            {"bVisible": false, "aTargets": [0]}
        ],
        ajax: "{{ route('admincomposer.getdata') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},
            {data: 'title', name: 'title'},
            /*{data: 'to', name: 'to'},*/
            {data: 'created_at', name: 'created_at'},
            {data: 'username', name: 'username'},
            {data: 'document', name: 'document'},
            {data: 'sendMs', name: 'sendMs'},
            {data: 'sent', name: 'sent'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        "oLanguage": {
            "sStripClasses": "",
            "sSearch": '',
            "sSearchPlaceholder": "@lang('app.search')"
        }
    });
</script>
@include('partials.customjs');
@endsection
