@extends('layouts.form')
@section('content')
        
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.settings.create')</a>
                    <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('usersettings.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                <form action="{{route('usersettings.store')}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                        
	                <div class="form-group">
                        <label class="control-label" for="company_name">@lang('main.settings.field.company_name')</label>
	                     <input id="company_name" name="company_name" class="form-control styler" type="text" maxlength="100"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="logo">@lang('main.settings.field.logo')</label>
	                     <br><input id="logo" name="logo"type="file" class="btn btn-default"/>
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="company_address1">@lang('main.settings.field.company_address1')</label>
	                     <input id="company_address1" name="company_address1" class="form-control styler" type="text" maxlength="200"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="company_address2">@lang('main.settings.field.company_address2')</label>
	                     <input id="company_address2" name="company_address2" class="form-control styler" type="text" maxlength="200"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="state">@lang('main.settings.field.state')</label>
	                     <input id="state" name="state" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="city">@lang('main.settings.field.city')</label>
	                     <input id="city" name="city" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="postcode">@lang('main.settings.field.postcode')</label>
	                     <input id="postcode" name="postcode" class="form-control styler" type="text" maxlength="20"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="country">@lang('main.settings.field.country')</label>
	                     <input id="country" name="country" class="form-control styler" type="text" maxlength="100"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="phone">@lang('main.settings.field.phone')</label>
	                     <input id="phone" name="phone" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="fax">@lang('main.settings.field.fax')</label>
	                     <input id="fax" name="fax" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="cemail">@lang('main.settings.field.cemail')</label>
	                     <input id="cemail" name="cemail" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="website">@lang('main.settings.field.website')</label>
	                     <input id="website" name="website" class="form-control styler" type="text" maxlength="100"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="primary_contact">@lang('main.settings.field.primary_contact')</label>
	                     <input id="primary_contact" name="primary_contact" class="form-control styler" type="text" maxlength="100"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="base_currency">@lang('main.settings.field.base_currency')</label>
	                     <input id="base_currency" name="base_currency" class="form-control styler" type="text" maxlength="20"  value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="vat_number">@lang('main.settings.field.vat_number')</label>
	                     <input id="vat_number" name="vat_number" class="form-control styler" type="text" maxlength="50"  value="" />
	                </div>

	                {{--<div class="form-group">
                        <label class="control-label" for="invoice_due_date">@lang('main.settings.field.invoice_due_date')</label>
	                     <input id="invoice_due_date" name="invoice_due_date" type="text" class="datepicker form-control styler" value="" />
	                </div>

	                <div class="form-group">
                        <label class="control-label" for="invoice_due_payment_by">@lang('main.settings.field.invoice_due_payment_by')</label>
	                     <input id="invoice_due_payment_by" name="invoice_due_payment_by" class="form-control styler" type="text" maxlength="200"  value="" />
	                </div>--}}

	                <div class="form-group">
                        <label class="control-label" for="message_title">@lang('main.settings.field.message_title')</label>
	                     <input id="message_title" name="message_title" class="form-control styler" type="text" maxlength="200"  value="" />
	                </div>

					<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-heading card-default">
									Message Body
								</div>
								<div class="card-block editor-fit">
									<textarea class="editor1" name="default_message2" ></textarea>
								</div>
							</div>
						</div>
					</div>
                     
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="card">
                              <div class="card-heading card-default">
                               @lang('main.settings.field.default_message')
                              </div>
                              <div class="card-block editor-fit">
                                <textarea class="editor1" name="default_message" ></textarea>
                              </div>
                           </div>
                        </div>
                     </div>                     

                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btnStatus">
                           @lang('app.add.btn')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
        
