
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | Exports
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
                                <a class="navbar-brand">Exports to CSV or SAGE</a>
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">

                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i> @lang('app.options')
                                        </button>
                                        <div class="dropdown-menu dropfix" aria-labelledby="dropdownOptions">
                                            <a class="dropdown-item" href="{{ route('sales.csvs.exports',['type'=>'xlsx','fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'sage'=>0]) }}">@lang('app.export.xlsx')</a>
                                            <a class="dropdown-item" href="{{ route('sales.csvs.exports',['type'=>'xls','fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'sage'=>0]) }}">@lang('app.export.xls')</a>
                                            <a class="dropdown-item" href="{{ route('sales.csvs.exports',['type'=>'csv','fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'sage'=>0]) }}">@lang('app.export.csv')</a>
                                            <a class="dropdown-item" href="{{ route('sales.csvs.exports',['type'=>'csv','fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'sage'=>1]) }}">Eport to Sage</a>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <div class="col-md-12">
                                    <form method="get" name="form1"  class="form-horizontal" tabindex="1">
                                        <div class="row">
                                            <div class="col-md-5 col-sm-12">
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

                                                        {{--@if($customer)
                                                            <input id="customersAutocomplete" name="customer"  class="form-control customer" value="{{$customerName}}"  placeholder="Customer Account" />
                                                        @else
                                                            <input id="customersAutocomplete" name="customer"  class="form-control customer"  placeholder="Customer Account" />
                                                        @endif

                                                        @if($invoice_no)
                                                            <input id="invoiceAutocomplete" name="invoice_no" class="form-control driver" value="{{$invoiceNo}}" placeholder="Invoice Number"/>
                                                        @else
                                                            <input id="invoiceAutocomplete" name="invoice_no" class="form-control driver" placeholder="Invoice Number"/>
                                                        @endif--}}


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2" style="padding-left:0;">
                                                <button type="submit" class="btn btn-info" name="search">View</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <table id="invoices_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                    <thead>
                                    <tr class="text-primary">
                                        <th>@lang('main.sales.field.job_date')</th>
                                        <th>Account</th>
                                        <th>@lang('main.sales.field.job_number')</th>
                                        {{--<th>@lang('main.sales.field.sender_reference')</th>--}}
                                        <th>@lang('main.sales.field.postcode')</th>
                                        <th>@lang('main.sales.field.service_type')</th>
                                        <th>@lang('main.sales.field.items')</th>
                                        <th>@lang('main.sales.field.weight')</th>
                                        <th>Charge</th>
                                        <th>Invoice</th>
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
        "order": [[0, "desc" ]],
        ajax: "{{ route('sales.search.export',['fromdate'=>$fromDate,'todate'=>$toDate, 'customer'=>$customer,'invoice'=>$invoice_no,'sage'=>$sage]) }}",
        columns: [
            {data: 'job_date', name: 'job_date'},
            {data: 'customer_account', name: 'customer_account'},
            {data: 'job_number', name: 'job_number'},
            /*{data: 'sender_reference', name: 'sender_reference'},*/
            {data: 'postcode', name: 'postcode'},
            {data: 'service_type', name: 'service_type'},
            {data: 'items', name: 'items'},
            {data: 'weight', name: 'weight'},
            {data: 'sub_total', name: 'sub_total'},
            {data: 'invoice', name: 'invoice'},
        ],
        "oLanguage": {
            "sStripClasses": "",
            "sSearch": '',
            "sSearchPlaceholder": "@lang('app.search')"
        }
    });
</script>
@include('partials.autocomplete');
@include('partials.customjs');
@endsection
