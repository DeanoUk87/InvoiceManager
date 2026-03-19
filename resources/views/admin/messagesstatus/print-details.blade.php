
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.messagesstatus.title')</h4>
<div class="vItems">
    <table class="table table-bordered" cellspacing="0">
        <tr>
            <td>@lang('main.messagesstatus.field.message_id')</td>
            <td>{{$messagesstatus->message_id}}</td>
        </tr>
       <tr>
            <td>@lang('main.messagesstatus.field.user_id')</td>
            <td>{{$messagesstatus->user_id}}</td>
        </tr>
       <tr>
            <td>@lang('main.messagesstatus.field.sent_status')</td>
            <td>{{$messagesstatus->sent_status}}</td>
        </tr>
       <tr>
            <td>@lang('main.messagesstatus.field.sent_at')</td>
            <td>{{$messagesstatus->sent_at}}</td>
        </tr>
       
    </table>
    
</div>

