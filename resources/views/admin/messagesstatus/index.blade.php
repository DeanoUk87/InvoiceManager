
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.messagesstatus.title')
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
    <form action="{{route('messagesstatus.deletemulti')}}" method="post" id="hezecomform" class="form-horizontal">
            {{ csrf_field() }}
        <div class="row mb-2 htsDisplay">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav  justify-content-between">
                            <a class="navbar-brand">@lang('main.messagesstatus.title')</a>
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
                                        <a class="dropdown-item" href="javascript:viod(0)" onclick="insertForm('{{route('messagesstatus.create')}}')">@lang('app.create')</a>
                                        <a class="dropdown-item" href="{{ route('messagesstatus.import.view') }}">@lang('app.import')</a>
                                        <a class="dropdown-item" href="{{ route('messagesstatus.export',['type'=>'xlsx']) }}">@lang('app.export.xlsx')</a>
                                        <a class="dropdown-item" href="{{ route('messagesstatus.export',['type'=>'xls']) }}">@lang('app.export.xls')</a>
                                        <a class="dropdown-item" href="{{ route('messagesstatus.export',['type'=>'csv']) }}">@lang('app.export.csv')</a>
                                        <a class="dropdown-item" href="{{route('messagesstatus.pdf')}}">@lang('app.export.pdf')</a>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <div class="vItems">
                        <div class="card-body" style="padding: 10px 0 10px 0">
                            <table id="messagesstatus_datatable"  class="table table-hover  table-responsive dt-responsive nowrap" cellspacing="0" style="width:100%">
                                <thead>
                                <tr class="text-primary">
                                    <td>
                                       <input type="checkbox" id="checkAll" class="check-style filled-in light-blue">
                                       <label for="checkAll" class="checklabel"></label>
                                    </td>
                                    <th>@lang('main.messagesstatus.field.message_id')</th>                  
                                <th>@lang('main.messagesstatus.field.user_id')</th>                  
                                <th>@lang('main.messagesstatus.field.sent_status')</th>                  
                                <th>@lang('main.messagesstatus.field.sent_at')</th>                  
                                
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
    var table = $('#messagesstatus_datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('messagesstatus.getdata') }}",
        columns: [
                 {data: 'checkbox', name: 'checkbox',orderable: false, searchable: false},             
                 {data: 'message_id', name: 'message_id'},              
                 {data: 'user_id', name: 'user_id'},              
                 {data: 'sent_status', name: 'sent_status'},              
                 {data: 'sent_at', name: 'sent_at'},              
            
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
