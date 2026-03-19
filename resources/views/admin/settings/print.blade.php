
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.settings.title')</h4>
<div class="vItems">
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th>@lang('main.settings.field.company_name')</th>                 
        <th>@lang('main.settings.field.company_address1')</th>                 
        <th>@lang('main.settings.field.company_address2')</th>                 
        <th>@lang('main.settings.field.state')</th>                 
        <th>@lang('main.settings.field.city')</th>                 
        <th>@lang('main.settings.field.postcode')</th>                 
        <th>@lang('main.settings.field.country')</th>                 
        <th>@lang('main.settings.field.phone')</th>                 
        <th>@lang('main.settings.field.fax')</th>                 
        <th>@lang('main.settings.field.cemail')</th>                 
        <th>@lang('main.settings.field.website')</th>                 
        <th>@lang('main.settings.field.primary_contact')</th>                 
        <th>@lang('main.settings.field.base_currency')</th>                 
        <th>@lang('main.settings.field.vat_number')</th>                 
        <th>@lang('main.settings.field.invoice_due_date')</th>                 
        <th>@lang('main.settings.field.invoice_due_payment_by')</th>                 
        <th>@lang('main.settings.field.message_title')</th>                 
        <th>@lang('main.settings.field.default_message')</th>                 
        <th>@lang('main.settings.field.default_message2')</th>                 
        
        </tr>
        </thead>
        <tbody>
        @foreach($settings as $row)
            <tr>
                <td>{{ $row->company_name }}</td>              
            <td>{{ $row->company_address1 }}</td>              
            <td>{{ $row->company_address2 }}</td>              
            <td>{{ $row->state }}</td>              
            <td>{{ $row->city }}</td>              
            <td>{{ $row->postcode }}</td>              
            <td>{{ $row->country }}</td>              
            <td>{{ $row->phone }}</td>              
            <td>{{ $row->fax }}</td>              
            <td>{{ $row->cemail }}</td>              
            <td>{{ $row->website }}</td>              
            <td>{{ $row->primary_contact }}</td>              
            <td>{{ $row->base_currency }}</td>              
            <td>{{ $row->vat_number }}</td>              
            <td>{{ $row->invoice_due_date }}</td>              
            <td>{{ $row->invoice_due_payment_by }}</td>              
            <td>{{ $row->message_title }}</td>              
            <td>{!! $row->default_message !!}</td>              
            <td>{!! $row->default_message2 !!}</td>              
            
            </tr>
        @endforeach
        </tbody>
    </table>
</div>