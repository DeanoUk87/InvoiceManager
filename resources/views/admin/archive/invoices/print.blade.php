
<link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/reporting.css') }}"/>
<h4>@lang('main.invoices.title')</h4>
<div class="vItems">
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th>@lang('main.invoices.field.sales_id')</th>                 
        <th>@lang('main.invoices.field.customer_account')</th>                 
        <th>@lang('main.invoices.field.invoice_number')</th>                 
        <th>@lang('main.invoices.field.invoice_date')</th>                 
        <th>@lang('main.invoices.field.due_date')</th>                 
        <th>@lang('main.invoices.field.date_created')</th>                 
        <th>@lang('main.invoices.field.terms')</th>                 
        <th>@lang('main.invoices.field.printer')</th>                 
        <th>@lang('main.invoices.field.po_number')</th>                 
        <th>@lang('main.invoices.field.num')</th>                 
        
        </tr>
        </thead>
        <tbody>
        @foreach($invoices as $row)
            <tr>
                <td>{{ $row->sales_id }}</td>              
            <td>{{ $row->customer_account }}</td>              
            <td>{{ $row->invoice_number }}</td>              
            <td>{{ $row->invoice_date }}</td>              
            <td>{{ $row->due_date }}</td>              
            <td>{{ $row->date_created }}</td>              
            <td>{!! $row->terms !!}</td>              
            <td>{{ $row->printer }}</td>              
            <td>{{ $row->po_number }}</td>              
            <td>{{ $row->num }}</td>              
            
            </tr>
        @endforeach
        </tbody>
    </table>
</div>