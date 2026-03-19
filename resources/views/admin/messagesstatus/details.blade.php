@extends('layouts.form')
@section('content')
        
<div class="row mb-2 viewDetails">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.messagesstatus.details')</a>
                    <div class="btn-group">
                        <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('messagesstatus.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                        <a href="{{route('messagesstatus.pdfdetails',['id'=>$messagesstatus->id])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <ul class="list-group">
                       
                    <li class="list-group-item">
                        <span>@lang('main.messagesstatus.field.message_id')</span>
                        <p>{{$messagesstatus->message_id}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.messagesstatus.field.user_id')</span>
                        <p>{{$messagesstatus->user_id}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.messagesstatus.field.sent_status')</span>
                        <p>{{$messagesstatus->sent_status}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.messagesstatus.field.sent_at')</span>
                        <p>{{$messagesstatus->sent_at}}</p>
                    </li>
                
                   
                </ul>
                
            </div>
        </div>
    </div>
</div>

@endsection
        
