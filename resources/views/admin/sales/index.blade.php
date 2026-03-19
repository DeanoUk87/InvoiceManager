
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.sales.title')
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
            <div class="row mb-2 htsDisplay">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <nav class="nav  justify-content-between">
                                @if($type=='job')
                                    <a class="navbar-brand">Job Search</a>
                                @else
                                    <a class="navbar-brand">@lang('main.sales.title')</a>
                                @endif
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        @role('admin')
                                        @if(!$type)
                                            <a class="btn btn-danger btn-sm"  href="{{route('sales.truncate')}}" onclick="return confirm('Are you sure you want to clear all data in this table?');">
                                                <i class="fa fa-trash"></i> Truncate
                                            </a>
                                        @endif
                                        @endrole
                                        @if(!$type)
                                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cog"></i> @lang('app.options')
                                            </button>
                                            <div class="dropdown-menu dropfix" aria-labelledby="dropdownOptions">
                                                <a class="dropdown-item" href="javascript:viod(0)" onclick="insertForm('{{route('sales.create')}}')">@lang('app.create')</a>
                                                <a class="dropdown-item" href="{{ route('sales.import.view') }}">@lang('app.import')</a>
                                                <a class="dropdown-item" href="{{ route('sales.export',['type'=>'xlsx']) }}">@lang('app.export.xlsx')</a>
                                                {{--<a class="dropdown-item" href="{{ route('sales.export',['type'=>'xls']) }}">@lang('app.export.xls')</a>--}}
                                                <a class="dropdown-item" href="{{ route('sales.export',['type'=>'csv']) }}">@lang('app.export.csv')</a>
                                                {{--<a class="dropdown-item" href="{{route('sales.pdf')}}">@lang('app.export.pdf')</a>--}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <div class="col-md-12">
                                    <form method="get" name="form1"  class="form-horizontal" action="{{route('sales.archive')}}">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <div class="input-group m-b">
                                                        <span class="input-group-addon "><i class="fa fa-calendar fa fa-calendar"></i></span>
                                                        @if($fromDate)
                                                            <input type="text"  class="form-control date1" name="date1" value="{{$fromDate}}" />
                                                            <input type="text"  class="form-control date1" name="date2" value="{{$toDate}}" />
                                                        @else
                                                            <input type="text"  class="form-control date1" name="date1"  value=""/>
                                                            <input type="text"  class="form-control date1" name="date2"  />
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" style="padding-left:0;">
                                                <button type="submit" class="btn btn-info" name="search">Archive Date Range</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <form action="{{route('sales.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
                                    {{ csrf_field() }}
                                    @role('admin')
                                    <button type="submit" class="btnDelete btn btn-danger btn-sm" name="btn-delete" id="btnStatus" style="display: none">
                                        <span class="fa fa-trash"></span> @lang('app.delete')
                                    </button>
                                    @endrole
                                <table id="sales_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                    <thead>
                                    <tr class="text-primary">
                                        <td>
                                            <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                            <label for="checkAll" class="checklabel"></label>
                                        </td>
                                        <th>@lang('main.sales.field.invoice_date')</th>
                                        <th>Account</th>
                                        <th>@lang('main.sales.field.job_number')</th>
                                        {{--<th>@lang('main.sales.field.sender_reference')</th>--}}
                                        <th>@lang('main.sales.field.postcode')</th>
                                        <th>@lang('main.sales.field.service_type')</th>
                                        <th>@lang('main.sales.field.items')</th>
                                        <th>@lang('main.sales.field.weight')</th>
                                        <th>Charge</th>
                                        {{--<th>Invoice</th>--}}
                                        @role('admin')
                                        <th>@lang('app.actions')</th>
                                        @endrole
                                    </tr>
                                    </thead>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@section('scripts');
<script type="text/javascript">
    var table = $('#sales_datatable').DataTable({
        processing: true,
        serverSide: true,
        iDisplayLength:25,
        searching: true,
        "order": [[1, "asc" ]],
        ajax: "{{ route('sales.getdata') }}",
        columns: [
            {data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},
            {data: 'invoice_date', name: 'invoice_date'},
            {data: 'customer_account', name: 'customer_account'},
            {data: 'job_number', name: 'job_number'},
            /*{data: 'sender_reference', name: 'sender_reference'},*/
            {data: 'postcode', name: 'postcode'},
            {data: 'service_type', name: 'service_type'},
            {data: 'items', name: 'items'},
            {data: 'weight', name: 'weight'},
            {data: 'sub_total', name: 'sub_total'},
            /*{data: 'invoice', name: 'invoice'},*/
            @if(Auth::user()->hasRole('admin'))
            {data: 'action', name: 'action', orderable: false, searchable: false}
            @endif
        ],
        "oLanguage": {
            "sStripClasses": "",
            "sSearch": '',
            "sSearchPlaceholder": "@if($type=='job')Search Job Number @else @lang('app.search') @endif"
        }
    });
</script>
@include('partials.customjs');
@endsection
