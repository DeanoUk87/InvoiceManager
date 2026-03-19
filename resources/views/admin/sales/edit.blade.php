@extends('layouts.form')
@section('content')
        
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.sales.update')</a>
                    <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('sales.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                <form action="{{route('sales.update',['id'=>$sales->sales_id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                     <input type="hidden" name="sales_id" value="{{$sales->sales_id}}">
	                <div class="form-group">
                        <label class="control-label" for="invoice_number">@lang('main.sales.field.invoice_number')</label>
	                     <input id="invoice_number" name="invoice_number" class="form-control styler" type="text" maxlength="11"  value="{{$sales->invoice_number}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="invoice_date">@lang('main.sales.field.invoice_date')</label>
	                     <input id="invoice_date" name="invoice_date" type="text" class="datepicker form-control styler" value="{{$sales->invoice_date}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="customer_account">@lang('main.sales.field.customer_account')</label>
	                     <input id="customer_account" name="customer_account" class="form-control styler" type="text" maxlength="100"  value="{{$sales->customer_account}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="customer_name">@lang('main.sales.field.customer_name')</label>
	                     <input id="customer_name" name="customer_name" class="form-control styler" type="text" maxlength="100"  value="{{$sales->customer_name}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="address1">@lang('main.sales.field.address1')</label>
	                     <input id="address1" name="address1" class="form-control styler" type="text" maxlength="200"  value="{{$sales->address1}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="address2">@lang('main.sales.field.address2')</label>
	                     <input id="address2" name="address2" class="form-control styler" type="text" maxlength="200"  value="{{$sales->address2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="town">@lang('main.sales.field.town')</label>
	                     <input id="town" name="town" class="form-control styler" type="text" maxlength="100"  value="{{$sales->town}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="country">@lang('main.sales.field.country')</label>
	                     <input id="country" name="country" class="form-control styler" type="text" maxlength="200"  value="{{$sales->country}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="postcode">@lang('main.sales.field.postcode')</label>
	                     <input id="postcode" name="postcode" class="form-control styler" type="text" maxlength="20"  value="{{$sales->postcode}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="spacer1">@lang('main.sales.field.spacer1')</label>
	                     <input id="spacer1" name="spacer1" class="form-control styler" type="text" maxlength="4"  value="{{$sales->spacer1}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="customer_account2">@lang('main.sales.field.customer_account2')</label>
	                     <input id="customer_account2" name="customer_account2" class="form-control styler" type="text" maxlength="100"  value="{{$sales->customer_account2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="numb1">@lang('main.sales.field.numb1')</label>
	                     <input id="numb1" name="numb1" class="form-control styler" type="text" maxlength="4"  value="{{$sales->numb1}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="items">@lang('main.sales.field.items')</label>
	                     <input id="items" name="items" class="form-control styler" type="text" maxlength="11"  value="{{$sales->items}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="weight">@lang('main.sales.field.weight')</label>
	                     <input id="weight" name="weight" class="form-control styler" type="text" maxlength="11"  value="{{$sales->weight}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="invoice_total">@lang('main.sales.field.invoice_total')</label>
	                     <input id="invoice_total" name="invoice_total" class="form-control styler" type="text" maxlength="11"  value="{{$sales->invoice_total}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="numb2">@lang('main.sales.field.numb2')</label>
	                     <input id="numb2" name="numb2" class="form-control styler" type="text" maxlength="4"  value="{{$sales->numb2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="spacer2">@lang('main.sales.field.spacer2')</label>
	                     <input id="spacer2" name="spacer2" class="form-control styler" type="text" maxlength="4"  value="{{$sales->spacer2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="job_number">@lang('main.sales.field.job_number')</label>
	                     <input id="job_number" name="job_number" class="form-control styler" type="text" maxlength="11"  value="{{$sales->job_number}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="job_date">@lang('main.sales.field.job_date')</label>
	                     <input id="job_date" name="job_date" type="text" class="datepicker form-control styler" value="{{$sales->job_date}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="sending_deport">@lang('main.sales.field.sending_deport')</label>
	                     <input id="sending_deport" name="sending_deport" class="form-control styler" type="text" maxlength="11"  value="{{$sales->sending_deport}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="delivery_deport">@lang('main.sales.field.delivery_deport')</label>
	                     <input id="delivery_deport" name="delivery_deport" class="form-control styler" type="text" maxlength="11"  value="{{$sales->delivery_deport}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="destination">@lang('main.sales.field.destination')</label>
	                     <input id="destination" name="destination" class="form-control styler" type="text" maxlength="200"  value="{{$sales->destination}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="town2">@lang('main.sales.field.town2')</label>
	                     <input id="town2" name="town2" class="form-control styler" type="text" maxlength="100"  value="{{$sales->town2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="postcode2">@lang('main.sales.field.postcode2')</label>
	                     <input id="postcode2" name="postcode2" class="form-control styler" type="text" maxlength="20"  value="{{$sales->postcode2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="service_type">@lang('main.sales.field.service_type')</label>
	                     <input id="service_type" name="service_type" class="form-control styler" type="text" maxlength="50"  value="{{$sales->service_type}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="items2">@lang('main.sales.field.items2')</label>
	                     <input id="items2" name="items2" class="form-control styler" type="text" maxlength="11"  value="{{$sales->items2}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="volume_weight">@lang('main.sales.field.volume_weight')</label>
	                     <input id="volume_weight" name="volume_weight" class="form-control styler" type="text" maxlength="11"  value="{{$sales->volume_weight}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="numb3">@lang('main.sales.field.numb3')</label>
	                     <input id="numb3" name="numb3" class="form-control styler" type="text" maxlength="4"  value="{{$sales->numb3}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="increased_liability_cover">@lang('main.sales.field.increased_liability_cover')</label>
	                     <input id="increased_liability_cover" name="increased_liability_cover" class="form-control styler" type="text" maxlength="4"  value="{{$sales->increased_liability_cover}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="sub_total">@lang('main.sales.field.sub_total')</label>
	                     <input id="sub_total" name="sub_total" class="form-control styler" type="text" maxlength="4"  value="{{$sales->sub_total}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="spacer3">@lang('main.sales.field.spacer3')</label>
	                     <input id="spacer3" name="spacer3" class="form-control styler" type="text" maxlength="4"  value="{{$sales->spacer3}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="numb4">@lang('main.sales.field.numb4')</label>
	                     <input id="numb4" name="numb4" class="form-control styler" type="text" maxlength="4"  value="{{$sales->numb4}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="sender_reference">@lang('main.sales.field.sender_reference')</label>
	                     <input id="sender_reference" name="sender_reference" class="form-control styler" type="text" maxlength="100"  value="{{$sales->sender_reference}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="numb5">@lang('main.sales.field.numb5')</label>
	                     <input id="numb5" name="numb5" class="form-control styler" type="text" maxlength="4"  value="{{$sales->numb5}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="percentage_fuel_surcharge">@lang('main.sales.field.percentage_fuel_surcharge')</label>
	                     <input id="percentage_fuel_surcharge" name="percentage_fuel_surcharge" class="form-control styler" type="text" maxlength="4"  value="{{$sales->percentage_fuel_surcharge}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="spacer4">@lang('main.sales.field.spacer4')</label>
	                     <input id="spacer4" name="spacer4" class="form-control styler" type="text" maxlength="4"  value="{{$sales->spacer4}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="senders_postcode">@lang('main.sales.field.senders_postcode')</label>
	                     <input id="senders_postcode" name="senders_postcode" class="form-control styler" type="text" maxlength="20"  value="{{$sales->senders_postcode}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="vat_amount">@lang('main.sales.field.vat_amount')</label>
	                     <input id="vat_amount" name="vat_amount" class="form-control styler" type="text" maxlength="20"  value="{{$sales->vat_amount}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="vat_percent">@lang('main.sales.field.vat_percent')</label>
	                     <input id="vat_percent" name="vat_percent" class="form-control styler" type="text" maxlength="20"  value="{{$sales->vat_percent}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="uploadcode">@lang('main.sales.field.uploadcode')</label>
	                     <input id="uploadcode" name="uploadcode" class="form-control styler" type="text" maxlength="11"  value="{{$sales->uploadcode}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="ms_created">@lang('main.sales.field.ms_created')</label>
	                     <input id="ms_created" name="ms_created" type="text" class="datepicker form-control styler" value="{{$sales->ms_created}}" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="job_dat">@lang('main.sales.field.job_dat')</label>
	                     <input id="job_dat" name="job_dat" type="text" class="datepicker form-control styler" value="{{$sales->job_dat}}" />
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
        
