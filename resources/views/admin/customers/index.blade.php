
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.customers.title')
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
        <form action="{{route('customers.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row mb-2 htsDisplay">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <nav class="nav  justify-content-between">
                                <a class="navbar-brand">@lang('main.customers.title')</a>
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        @role('admin')
                                        <a class="btn btn-danger btn-sm"  href="{{route('customers.truncate')}}" onclick="return confirm('Are you sure you want to clear all data in this table?');">
                                            <i class="fa fa-trash"></i> Truncate
                                        </a>
                                        <button type="submit" class="btnDelete btn btn-danger btn-sm" name="btn-delete" id="btnStatus" style="display: none">
                                            <span class="fa fa-trash"></span> @lang('app.delete')
                                        </button>
                                        @endrole
                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i> @lang('app.options')
                                        </button>
                                        <div class="dropdown-menu dropfix" aria-labelledby="dropdownOptions">
                                            <a class="dropdown-item" href="javascript:viod(0)" onclick="insertForm('{{route('customers.create')}}')">@lang('app.create')</a>
                                            <a class="dropdown-item" href="{{ route('customers.import.view') }}">@lang('app.import')</a>
                                            <a class="dropdown-item" href="{{ route('customers.export',['type'=>'xlsx']) }}">@lang('app.export.xlsx')</a>
                                            <a class="dropdown-item" href="{{ route('customers.export',['type'=>'xls']) }}">@lang('app.export.xls')</a>
                                            <a class="dropdown-item" href="{{ route('customers.export',['type'=>'csv']) }}">@lang('app.export.csv')</a>
                                            <a class="dropdown-item" href="{{route('customers.pdf')}}">@lang('app.export.pdf')</a>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <table id="customers_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                    <thead>
                                    <tr class="text-primary">
                                        <td>
                                            <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                            <label for="checkAll" class="checklabel"></label>
                                        </td>
                                        <th>@lang('main.customers.field.customer_account')</th>
                                        <th>@lang('main.customers.field.customer_email')</th>
                                        <th>@lang('main.customers.field.customer_email_bcc')</th>
                                        <th>@lang('main.customers.field.customer_phone')</th>
                                        <th>Login Access</th>
                                        @if(Auth::user()->hasAnyRole(['admin','admin2']))
                                            <th>@lang('app.actions')</th>
                                        @endif
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
    var table = $('#customers_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('customers.getdata') }}",
        columns: [
            {data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},
            {data: 'customer_account', name: 'customer_account'},
            {data: 'customer_email', name: 'customer_email'},
            {data: 'customer_email_bcc', name: 'customer_email_bcc'},
            {data: 'customer_phone', name: 'customer_phone'},
            {data: 'access', name: 'access'},
            @if(Auth::user()->hasAnyRole(['admin','admin2']))
            {data: 'action', name: 'action', orderable: false, searchable: false}
            @endif
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
