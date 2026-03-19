
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.settings.title')
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
        <form action="{{route('usersettings.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row mb-2 htsDisplay">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <nav class="nav  justify-content-between">
                                <a class="navbar-brand">@lang('main.settings.title')</a>
                                <div class="hts-flash"></div>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="submit" class="btnDelete btn btn-danger btn-sm" name="btn-delete" id="btnStatus" style="display: none">
                                            <span class="fa fa-trash"></span> @lang('app.delete')
                                        </button>
                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i> @lang('app.options')
                                        </button>
                                        <div class="dropdown-menu dropfix" aria-labelledby="dropdownOptions">
                                            <a class="dropdown-item" href="javascript:viod(0)" onclick="insertForm('{{route('usersettings.create')}}')">@lang('app.create')</a>
                                            <a class="dropdown-item" href="{{ route('usersettings.import.view') }}">@lang('app.import')</a>
                                            <a class="dropdown-item" href="{{ route('usersettings.export',['type'=>'xlsx']) }}">@lang('app.export.xlsx')</a>
                                            <a class="dropdown-item" href="{{ route('usersettings.export',['type'=>'xls']) }}">@lang('app.export.xls')</a>
                                            <a class="dropdown-item" href="{{ route('usersettings.export',['type'=>'csv']) }}">@lang('app.export.csv')</a>
                                            <a class="dropdown-item" href="{{route('usersettings.pdf')}}">@lang('app.export.pdf')</a>
                                        </div>
                                    </div>
                                </div>
                            </nav>
                        </div>
                        <div class="vItems">
                            <div class="card-body" style="padding: 10px 0 10px 0">
                                <table id="settings_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                    <thead>
                                    <tr class="text-primary">
                                        <td>
                                            <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                            <label for="checkAll" class="checklabel"></label>
                                        </td>
                                        <th>@lang('main.settings.field.company_name')</th>
                                        <th>@lang('main.settings.field.company_address1')</th>
                                        <th>@lang('main.settings.field.company_address2')</th>
                                        <th>@lang('main.settings.field.state')</th>

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
    var table = $('#settings_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('usersettings.getdata') }}",
        columns: [
            {data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},
            {data: 'company_name', name: 'company_name'},
            {data: 'company_address1', name: 'company_address1'},
            {data: 'company_address2', name: 'company_address2'},
            {data: 'state', name: 'state'},

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
