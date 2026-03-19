
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.messagesstatus.title')</h4>
<div class="vItems">
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th>@lang('main.messagesstatus.field.message_id')</th>                 
        <th>@lang('main.messagesstatus.field.user_id')</th>                 
        <th>@lang('main.messagesstatus.field.sent_status')</th>                 
        <th>@lang('main.messagesstatus.field.sent_at')</th>                 
        
        </tr>
        </thead>
        <tbody>
        @foreach($messagesstatus as $row)
            <tr>
                <td>{{ $row->message_id }}</td>              
            <td>{{ $row->user_id }}</td>              
            <td>{{ $row->sent_status }}</td>              
            <td>{{ $row->sent_at }}</td>              
            
            </tr>
        @endforeach
        </tbody>
    </table>
</div>