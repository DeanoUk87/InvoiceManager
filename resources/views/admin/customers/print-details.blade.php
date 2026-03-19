
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.customers.title')</h4>
<div class="vItems">
    <table class="table table-bordered" cellspacing="0">
        <tr>
            <td>@lang('main.customers.field.user_id')</td>
            <td>{{$customers->user_id}}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.customer_account')</td>
            <td>{{$customers->customer_account}}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.customer_email')</td>
            <td>{{$customers->customer_email}}</td>
        </tr>
        <tr>
            <td>@lang('main.customers.field.customer_email_bcc')</td>
            <td>{{$customers->customer_email_bcc}}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.customer_phone')</td>
            <td>{{$customers->customer_phone}}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.terms_of_payment')</td>
            <td>{!!$customers->terms_of_payment!!}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.message_type')</td>
            <td>{{$customers->message_type}}</td>
        </tr>
       <tr>
            <td>@lang('main.customers.field.po_number')</td>
            <td>{{$customers->po_number}}</td>
        </tr>
       
    </table>
    
</div>

