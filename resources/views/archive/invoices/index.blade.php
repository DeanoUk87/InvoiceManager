
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.invoices.title')
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
        {{-- <form action="{{route('invoices.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
             {{ csrf_field() }}--}}
        <div class="row mb-2 htsDisplay">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav  justify-content-between">
                            @if($printer==2)
                                <a class="navbar-brand">Printed @lang('main.invoices.title')</a>
                            @elseif($printer==1)
                                <a class="navbar-brand">Unprinted @lang('main.invoices.title') <small style="font-size: 12px;">You send unprinted invoices to customers with email address</small></a>
                            @else
                                <a class="navbar-brand">@lang('main.invoices.title') <small>Filter By (Date or Account or Invoice No.)</small></a>
                            @endif
                            <div class="hts-flash"></div>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button type="submit" class="btnDelete btn btn-danger btn-sm" name="btn-delete" id="btnStatus" style="display: none">
                                        <span class="fa fa-trash"></span> @lang('app.delete')
                                    </button>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <div class="vItems">
                        <div class="card-body" style="padding: 10px 0 10px 0">
                            @if(!$printer)
                                <div class="col-md-12">
                                    <form method="get" name="form1"  class="form-horizontal" tabindex="1">
                                        <div class="row">
                                            <div class="col-md-9 col-sm-12">
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
                                                <button type="submit" class="btn btn-info" name="search">View</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            <table id="invoices_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                <thead>
                                <tr class="text-primary">
                                    {{--<td>
                                        <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                        <label for="checkAll" class="checklabel"></label>
                                    </td>--}}
                                    {{--<th>@lang('main.invoices.field.sales_id')</th>--}}
                                    <th>@lang('main.invoices.field.customer_account')</th>
                                    <th>@lang('main.invoices.field.invoice_number')</th>
                                    <th>@lang('main.invoices.field.invoice_date')</th>
                                    <th>@lang('main.invoices.field.due_date')</th>
                                    <th>Preview</th>
                                    @if(Auth::user()->hasAnyRole(['admin','admin2']))
                                    {{--<th>@lang('app.actions')</th>--}}
                                    @endif
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- </form>--}}
    </div>
@endsection

@section('scripts');
<script type="text/javascript">
    var table = $('#invoices_datatable').DataTable({
        processing: true,
        serverSide: true,
        iDisplayLength:25,
        searching: true,
        "order": [[0, "asc" ]],
        ajax: "{{ route('archive.invoices.search',['fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'printer'=>$printer]) }}",
        {{--ajax: "{{ route('invoices.getdata') }}",--}}
        columns: [
            /*{data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},*/
            /*{data: 'sales_id', name: 'sales_id'},*/
            {data: 'customer_account', name: 'customer_account'},
            {data: 'invoice_number', name: 'invoice_number'},
            {data: 'invoice_date', name: 'invoice_date'},
            {data: 'due_date', name: 'due_date'},
            {data: 'invoice', name: 'invoice'},
            @if(Auth::user()->hasAnyRole(['admin','admin2']))
            /*{data: 'action', name: 'action', orderable: false, searchable: false}*/
            @endif
        ],
        "oLanguage": {
            "sStripClasses": "",
            "sSearch": '',
            "sSearchPlaceholder": "AccountNo | InvoiceNo"
        }
    });
</script>
@include('partials.autocomplete');
@include('partials.customjs');
@endsection
