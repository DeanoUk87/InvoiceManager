
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.sales.title')</h4>
<div class="vItems">
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th>@lang('main.sales.field.invoice_number')</th>                 
        <th>@lang('main.sales.field.invoice_date')</th>                 
        <th>@lang('main.sales.field.customer_account')</th>                 
        <th>@lang('main.sales.field.customer_name')</th>                 
        <th>@lang('main.sales.field.address1')</th>                 
        <th>@lang('main.sales.field.address2')</th>                 
        <th>@lang('main.sales.field.town')</th>                 
        <th>@lang('main.sales.field.country')</th>                 
        <th>@lang('main.sales.field.postcode')</th>                 
        <th>@lang('main.sales.field.spacer1')</th>                 
        <th>@lang('main.sales.field.customer_account2')</th>                 
        <th>@lang('main.sales.field.numb1')</th>                 
        <th>@lang('main.sales.field.items')</th>                 
        <th>@lang('main.sales.field.weight')</th>                 
        <th>@lang('main.sales.field.invoice_total')</th>                 
        <th>@lang('main.sales.field.numb2')</th>                 
        <th>@lang('main.sales.field.spacer2')</th>                 
        <th>@lang('main.sales.field.job_number')</th>                 
        <th>@lang('main.sales.field.job_date')</th>                 
        <th>@lang('main.sales.field.sending_deport')</th>                 
        <th>@lang('main.sales.field.delivery_deport')</th>                 
        <th>@lang('main.sales.field.destination')</th>                 
        <th>@lang('main.sales.field.town2')</th>                 
        <th>@lang('main.sales.field.postcode2')</th>                 
        <th>@lang('main.sales.field.service_type')</th>                 
        <th>@lang('main.sales.field.items2')</th>                 
        <th>@lang('main.sales.field.volume_weight')</th>                 
        <th>@lang('main.sales.field.numb3')</th>                 
        <th>@lang('main.sales.field.increased_liability_cover')</th>                 
        <th>@lang('main.sales.field.sub_total')</th>                 
        <th>@lang('main.sales.field.spacer3')</th>                 
        <th>@lang('main.sales.field.numb4')</th>                 
        <th>@lang('main.sales.field.sender_reference')</th>                 
        <th>@lang('main.sales.field.numb5')</th>                 
        <th>@lang('main.sales.field.percentage_fuel_surcharge')</th>                 
        <th>@lang('main.sales.field.spacer4')</th>                 
        <th>@lang('main.sales.field.senders_postcode')</th>                 
        <th>@lang('main.sales.field.vat_amount')</th>                 
        <th>@lang('main.sales.field.vat_percent')</th>                 
        <th>@lang('main.sales.field.uploadcode')</th>                 
        <th>@lang('main.sales.field.ms_created')</th>                 
        <th>@lang('main.sales.field.job_dat')</th>                 
        
        </tr>
        </thead>
        <tbody>
        @foreach($sales as $row)
            <tr>
                <td>{{ $row->invoice_number }}</td>              
            <td>{{ $row->invoice_date }}</td>              
            <td>{{ $row->customer_account }}</td>              
            <td>{{ $row->customer_name }}</td>              
            <td>{{ $row->address1 }}</td>              
            <td>{{ $row->address2 }}</td>              
            <td>{{ $row->town }}</td>              
            <td>{{ $row->country }}</td>              
            <td>{{ $row->postcode }}</td>              
            <td>{{ $row->spacer1 }}</td>              
            <td>{{ $row->customer_account2 }}</td>              
            <td>{{ $row->numb1 }}</td>              
            <td>{{ $row->items }}</td>              
            <td>{{ $row->weight }}</td>              
            <td>{{ $row->invoice_total }}</td>              
            <td>{{ $row->numb2 }}</td>              
            <td>{{ $row->spacer2 }}</td>              
            <td>{{ $row->job_number }}</td>              
            <td>{{ $row->job_date }}</td>              
            <td>{{ $row->sending_deport }}</td>              
            <td>{{ $row->delivery_deport }}</td>              
            <td>{{ $row->destination }}</td>              
            <td>{{ $row->town2 }}</td>              
            <td>{{ $row->postcode2 }}</td>              
            <td>{{ $row->service_type }}</td>              
            <td>{{ $row->items2 }}</td>              
            <td>{{ $row->volume_weight }}</td>              
            <td>{{ $row->numb3 }}</td>              
            <td>{{ $row->increased_liability_cover }}</td>              
            <td>{{ $row->sub_total }}</td>              
            <td>{{ $row->spacer3 }}</td>              
            <td>{{ $row->numb4 }}</td>              
            <td>{{ $row->sender_reference }}</td>              
            <td>{{ $row->numb5 }}</td>              
            <td>{{ $row->percentage_fuel_surcharge }}</td>              
            <td>{{ $row->spacer4 }}</td>              
            <td>{{ $row->senders_postcode }}</td>              
            <td>{{ $row->vat_amount }}</td>              
            <td>{{ $row->vat_percent }}</td>              
            <td>{{ $row->uploadcode }}</td>              
            <td>{{ $row->ms_created }}</td>              
            <td>{{ $row->job_dat }}</td>              
            
            </tr>
        @endforeach
        </tbody>
    </table>
</div>