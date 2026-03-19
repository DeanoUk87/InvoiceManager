@extends('layouts.form')
@section('content')
        
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.messagesstatus.update')</a>
                    <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('messagesstatus.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                <form action="{{route('messagesstatus.update',['id'=>$messagesstatus->id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                     <input type="hidden" name="id" value="{{$messagesstatus->id}}">
	                <div class="form-group">
                        <label class="control-label" for="message_id">@lang('main.messagesstatus.field.message_id')</label>
	                     <input id="message_id" name="message_id" class="form-control styler" type="text" maxlength="11"  value="{{$messagesstatus->message_id}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="user_id">@lang('main.messagesstatus.field.user_id')</label>
	                     <input id="user_id" name="user_id" class="form-control styler" type="text" maxlength="11"  value="{{$messagesstatus->user_id}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="sent_status">@lang('main.messagesstatus.field.sent_status')</label>
	                     <input id="sent_status" name="sent_status" class="form-control styler" type="text" maxlength="4"  value="{{$messagesstatus->sent_status}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="sent_at">@lang('main.messagesstatus.field.sent_at')</label>
	                     <input id="sent_at" name="sent_at" type="text" class="datepicker form-control styler" value="{{$messagesstatus->sent_at}}" />
	                </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-update" id="btnStatus">
                           @lang('app.update.btn')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
        
