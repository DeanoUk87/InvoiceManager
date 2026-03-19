@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('app.dashboard')
@endsection

@section('content')

    @if(Auth::user()->hasRole('admin'))
    <div class="dashboard-stat">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <h4 class="text-primary">
                                    <i class="fa fa-hand-o-up highlight-icon purple" aria-hidden="true"></i>
                                </h4>
                            </div>
                            <div class="float-right">
                                <p class="card-text text-dark">Customers</p>
                                <h5 class="bold-text">{{$customers}}</h5>
                            </div>
                        </div>
                        <p class="text-muted">
                            <i class="fa fa-repeat mr-1" aria-hidden="true"></i> Total Customers
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card card-statistics ">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <h4 class="text-primary">
                                    <i class="fa fa-briefcase highlight-icon amber" aria-hidden="true"></i>
                                </h4>
                            </div>
                            <div class="float-right">
                                <p class="card-text text-dark">Invoices</p>
                                <h5 class="bold-text">{{$invoices}}</h5>
                            </div>
                        </div>
                        <p class="text-muted">
                            <i class="fa fa-repeat mr-1" aria-hidden="true"></i> Total Invoices
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

        <div class="card-deck">
            <div class="card col-lg-12 px-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title">Latest Invoice</h5>
                    <div class="table-responsive">
                        <table id="invoices_datatable" class="table center-aligned-table">
                            <thead>
                            <tr class="text-primary">
                                <th>@lang('main.invoices.field.customer_account')</th>
                                <th>@lang('main.invoices.field.invoice_number')</th>
                                <th>@lang('main.invoices.field.invoice_date')</th>
                                <th>@lang('main.invoices.field.due_date')</th>
                                @if(Auth::user()->hasRole('admin'))
                                <th>Status</th>
                                @endif
                                <th>Preview</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--@else
        @include('sysadmin.profile.update')
    @endif--}}
@endsection


@section('scripts');
<script type="text/javascript">
    var table = $('#invoices_datatable').DataTable({
        processing: true,
        serverSide: true,
        "order": [[0, "desc" ]],
        ajax: "{{ route('invoices.search',['fromdate'=>0,'todate'=>0, 'customer'=>0,'invoice'=>0,'printer'=>0]) }}",
        columns: [
            {data: 'customer_account', name: 'customer_account'},
            {data: 'invoice_number', name: 'invoice_number'},
            {data: 'invoice_date', name: 'invoice_date'},
            {data: 'due_date', name: 'due_date'},
            @if(Auth::user()->hasRole('admin'))
            {data: 'printer', name: 'printer'},
            @endif
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

