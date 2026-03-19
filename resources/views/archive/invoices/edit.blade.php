@extends('layouts.form')
@section('content')
        
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.invoices.update')</a>
                    <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('invoices.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                <form action="{{route('invoices.update',['id'=>$invoices->invoice_id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                     <input type="hidden" name="invoice_id" value="{{$invoices->invoice_id}}">
	                <div class="form-group">
                        <label class="control-label" for="sales_id">@lang('main.invoices.field.sales_id')</label>
	                     <input id="sales_id" name="sales_id" class="form-control styler" type="text" maxlength="11"  value="{{$invoices->sales_id}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="customer_account">@lang('main.invoices.field.customer_account')</label>
	                     <input id="customer_account" name="customer_account" class="form-control styler" type="text" maxlength="50"  value="{{$invoices->customer_account}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="invoice_number">@lang('main.invoices.field.invoice_number')</label>
	                     <input id="invoice_number" name="invoice_number" class="form-control styler" type="text" maxlength="50"  value="{{$invoices->invoice_number}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="invoice_date">@lang('main.invoices.field.invoice_date')</label>
	                     <input id="invoice_date" name="invoice_date" type="text" class="datepicker form-control styler" value="{{$invoices->invoice_date}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="due_date">@lang('main.invoices.field.due_date')</label>
	                     <input id="due_date" name="due_date" type="text" class="datepicker form-control styler" value="{{$invoices->due_date}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="date_created">@lang('main.invoices.field.date_created')</label>
	                     <input id="date_created" name="date_created" type="text" class="datepicker form-control styler" value="{{$invoices->date_created}}" />
	                </div>
                     
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="card">
                              <div class="card-heading card-default">
                               @lang('main.invoices.field.terms')
                              </div>
                              <div class="card-block editor-fit">
                                <textarea class="editor1" name="terms" >{{$invoices->terms}}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
	                <div class="form-group">
                        <label class="control-label" for="printer">@lang('main.invoices.field.printer')</label>
	                     <input id="printer" name="printer" class="form-control styler" type="text" maxlength="5"  value="{{$invoices->printer}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="po_number">@lang('main.invoices.field.po_number')</label>
	                     <input id="po_number" name="po_number" class="form-control styler" type="text" maxlength="50"  value="{{$invoices->po_number}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="num">@lang('main.invoices.field.num')</label>
	                     <input id="num" name="num" class="form-control styler" type="text" maxlength="50"  value="{{$invoices->num}}" />
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
        
