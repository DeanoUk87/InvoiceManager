
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
                                    <a class="navbar-brand text-danger">@lang('main.sales.title') Archive</a>
                                @endif
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        @role('admin')
                                        @if(!$type)
                                            <a class="btn btn-danger btn-sm"  href="{{route('archive-sales.truncate')}}" onclick="return confirm('Are you sure you want to clear all data in this table?');">
                                                <i class="fa fa-trash"></i> Truncate
                                            </a>
                                        @endif
                                        @endrole
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <form action="{{route('archive-sales.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
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
        ajax: "{{ route('archive-sales.getdata') }}",
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
