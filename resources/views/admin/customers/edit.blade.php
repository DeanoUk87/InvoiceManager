@extends('layouts.form')

@section('content')

        

<div class="row mb-2 htsDisplay">

    <div class="col-lg-12">

        <div class="card">

            <div class="card-header" style="">

                <nav class="nav  justify-content-between">

                    <a class="navbar-brand">@lang('main.customers.update')</a>

                    <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('customers.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>

                </nav>

            </div>

            <div class="card-body">

                <div class="hts-flash"></div>

                <form action="{{route('customers.update',['id'=>$customers->contact_id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">

                    {{ csrf_field() }}

                     <input type="hidden" name="contact_id" value="{{$customers->contact_id}}">



	                <div class="form-group">

                        <label class="control-label" for="customer_account">@lang('main.customers.field.customer_account')</label>

	                     <input id="customer_account" name="customer_account" class="form-control styler" type="text" maxlength="100"  value="{{$customers->customer_account}}" />

	                </div>



	                <div class="form-group">

                        <label class="control-label" for="customer_email">@lang('main.customers.field.customer_email')</label>

	                     <input id="customer_email" name="customer_email" class="form-control styler" type="text" maxlength="200"  value="{{$customers->customer_email}}" />

	                </div>



                    <div class="form-group">

                        <label class="control-label" for="customer_email_bcc">@lang('main.customers.field.customer_email_bcc')</label>

                        <input id="customer_email_bcc" name="customer_email_bcc" class="form-control styler" type="text" maxlength="100"  value="{{$customers->customer_email_bcc}}" />

                    </div>



	                <div class="form-group">

                        <label class="control-label" for="customer_phone">@lang('main.customers.field.customer_phone')</label>

	                     <input id="customer_phone" name="customer_phone" class="form-control styler" type="text" maxlength="50"  value="{{$customers->customer_phone}}" />

	                </div>

                     

                     <div class="row">

                        <div class="col-sm-12">

                           <div class="card">

                              <div class="card-heading card-default">

                               @lang('main.customers.field.terms_of_payment')

                              </div>

                              <div class="card-block editor-fit">

                                <textarea class="editor1" name="terms_of_payment" >{{$customers->terms_of_payment}}</textarea>

                              </div>

                           </div>

                        </div>

                     </div>

	                {{--<div class="form-group">

                        <label class="control-label" for="message_type">@lang('main.customers.field.message_type')</label>

	                     <input id="message_type" name="message_type" class="form-control styler" type="text" maxlength="11"  value="{{$customers->message_type}}" />

	                </div>--}}



	                <div class="form-group">

                        <label class="control-label" for="po_number">@lang('main.customers.field.po_number')</label>

	                     <input id="po_number" name="po_number" class="form-control styler" type="text" maxlength="50"  value="{{$customers->po_number}}" />

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

        

