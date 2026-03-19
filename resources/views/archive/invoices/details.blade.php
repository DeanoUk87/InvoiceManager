@extends('layouts.form')
@section('content')
        
<div class="row mb-2 viewDetails">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.invoices.details')</a>
                    <div class="btn-group">
                        <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('invoices.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                        <a href="{{route('invoices.pdfdetails',['id'=>$invoices->invoice_id])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <ul class="list-group">
                       
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.sales_id')</span>
                        <p>{{$invoices->sales_id}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.customer_account')</span>
                        <p>{{$invoices->customer_account}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.invoice_number')</span>
                        <p>{{$invoices->invoice_number}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.invoice_date')</span>
                        <p>{{$invoices->invoice_date}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.due_date')</span>
                        <p>{{$invoices->due_date}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.date_created')</span>
                        <p>{{$invoices->date_created}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.terms')</span>
                        <p>{!!$invoices->terms!!}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.printer')</span>
                        <p>{{$invoices->printer}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.po_number')</span>
                        <p>{{$invoices->po_number}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.invoices.field.num')</span>
                        <p>{{$invoices->num}}</p>
                    </li>
                
                   
                </ul>
                
            </div>
        </div>
    </div>
</div>

@endsection
        
