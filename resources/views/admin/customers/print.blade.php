
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.customers.title')</h4>
<div class="vItems">
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th>@lang('main.customers.field.user_id')</th>
            <th>@lang('main.customers.field.customer_account')</th>
            <th>@lang('main.customers.field.customer_email')</th>
            <th>@lang('main.customers.field.customer_email_bcc')</th>
            <th>@lang('main.customers.field.customer_phone')</th>
            <th>@lang('main.customers.field.terms_of_payment')</th>
            <th>@lang('main.customers.field.message_type')</th>
            <th>@lang('main.customers.field.po_number')</th>

        </tr>
        </thead>
        <tbody>
        @foreach($customers as $row)
            <tr>
                <td>{{ $row->user_id }}</td>
                <td>{{ $row->customer_account }}</td>
                <td>{{ $row->customer_email }}</td>
                <td>{{ $row->customer_email_bcc }}</td>
                <td>{{ $row->customer_phone }}</td>
                <td>{!! $row->terms_of_payment !!}</td>
                <td>{{ $row->message_type }}</td>
                <td>{{ $row->po_number }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>