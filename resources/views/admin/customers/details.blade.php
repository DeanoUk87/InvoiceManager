@extends('layouts.form')
@section('content')
        
<div class="row mb-2 viewDetails">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.customers.details')</a>
                    <div class="btn-group">
                        <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('customers.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                        <a href="{{route('customers.pdfdetails',['id'=>$customers->contact_id])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <ul class="list-group">
                       
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.user_id')</span>
                        <p>{{$customers->user_id}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.customer_account')</span>
                        <p>{{$customers->customer_account}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.customer_email')</span>
                        <p>{{$customers->customer_email}}</p>
                    </li>
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.customer_email_bcc')</span>
                        <p>{{$customers->customer_email}}</p>
                    </li>
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.customer_phone')</span>
                        <p>{{$customers->customer_phone}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.terms_of_payment')</span>
                        <p>{!!$customers->terms_of_payment!!}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.message_type')</span>
                        <p>{{$customers->message_type}}</p>
                    </li>
                    
                    <li class="list-group-item">
                        <span>@lang('main.customers.field.po_number')</span>
                        <p>{{$customers->po_number}}</p>
                    </li>
                
                   
                </ul>
                
            </div>
        </div>
    </div>
</div>

@endsection
        
