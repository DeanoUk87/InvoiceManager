
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.settings.title')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">@lang('main.settings.title')</a>
                    </nav>
                </div>
                <div class="card-body">
                    <div class="hts-flash"></div>
                    <form action="{{route('usersettings.update',['id'=>$settings->id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{$settings->id}}">

                        @if(Auth::user()->hasRole('admin2'))
                            <div class="form-group" style="display: none;">
                                <label class="control-label" for="company_name">@lang('main.settings.field.company_name')</label>
                                <input id="company_name" name="company_name" class="form-control styler" type="text" maxlength="100"  value="{{$settings->company_name}}" />
                            </div>
                            <div class="form-group" style="display: none;">
                                <label class="control-label sr-only" for="send_limit">Invoice Send Limit (If empty the system will use the default limit setting)</label>
                                <input id="send_limit" name="send_limit" class="form-control styler" type="number" value="{{$settings->send_limit}}" />
                            </div>
                        @endif

                        @if(Auth::user()->hasRole('admin'))
                            <div class="form-group">
                                <label class="control-label" for="company_name">@lang('main.settings.field.company_name')</label>
                                <input id="company_name" name="company_name" class="form-control styler" type="text" maxlength="100"  value="{{$settings->company_name}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="send_limit">Invoice Send Limit (If empty the system will use the default limit setting)</label>
                                <input id="send_limit" name="send_limit" class="form-control styler" type="number" value="{{$settings->send_limit}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="logo">@lang('main.settings.field.logo')</label>
                                <br><input id="logo" name="logo"type="file" class="btn btn-default"/>
                                @if($settings->logo and file_exists(base_path('public/uploads/'.$settings->logo)))
                                    <div class="app-gallery">
                                        <div class="row" data-id="row-{{$settings->id}}">
                                            <div class="col-lg-2 col-md-3 col-xs-6">
                                                <a class="lightbox" href="{{ asset('public/uploads')}}/{{$settings->logo}}">
                                                    <img class="file-width" src="{{ asset('public/uploads')}}/{{$settings->logo}}" alt="">
                                                </a>
                                                <a href="javascript:viod(0);" onclick="deleteFile('{{ url('admin/settings/deletefile') }}','{{$settings->id}}')"  class="btn btn-danger btn-sm" style="position: absolute; top:2px; left:15px"><i class="fa fa-trash fa-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="company_address1">@lang('main.settings.field.company_address1')</label>
                                <input id="company_address1" name="company_address1" class="form-control styler" type="text" maxlength="200"  value="{{$settings->company_address1}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="company_address2">@lang('main.settings.field.company_address2')</label>
                                <input id="company_address2" name="company_address2" class="form-control styler" type="text" maxlength="200"  value="{{$settings->company_address2}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="state">@lang('main.settings.field.state')</label>
                                <input id="state" name="state" class="form-control styler" type="text" maxlength="50"  value="{{$settings->state}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="city">@lang('main.settings.field.city')</label>
                                <input id="city" name="city" class="form-control styler" type="text" maxlength="50"  value="{{$settings->city}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="postcode">@lang('main.settings.field.postcode')</label>
                                <input id="postcode" name="postcode" class="form-control styler" type="text" maxlength="20"  value="{{$settings->postcode}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="country">@lang('main.settings.field.country')</label>
                                <input id="country" name="country" class="form-control styler" type="text" maxlength="100"  value="{{$settings->country}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="phone">@lang('main.settings.field.phone')</label>
                                <input id="phone" name="phone" class="form-control styler" type="text" maxlength="50"  value="{{$settings->phone}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="fax">@lang('main.settings.field.fax')</label>
                                <input id="fax" name="fax" class="form-control styler" type="text" maxlength="50"  value="{{$settings->fax}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="cemail">@lang('main.settings.field.cemail')</label>
                                <input id="cemail" name="cemail" class="form-control styler" type="text" maxlength="50"  value="{{$settings->cemail}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="website">@lang('main.settings.field.website')</label>
                                <input id="website" name="website" class="form-control styler" type="text" maxlength="100"  value="{{$settings->website}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="primary_contact">@lang('main.settings.field.primary_contact')</label>
                                <input id="primary_contact" name="primary_contact" class="form-control styler" type="text" maxlength="100"  value="{{$settings->primary_contact}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="base_currency">@lang('main.settings.field.base_currency')</label>
                                <input id="base_currency" name="base_currency" class="form-control styler" type="text" maxlength="20"  value="{{$settings->base_currency}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="vat_number">@lang('main.settings.field.vat_number')</label>
                                <input id="vat_number" name="vat_number" class="form-control styler" type="text" maxlength="50"  value="{{$settings->vat_number}}" />
                            </div>

                            {{--<div class="form-group">
                                <label class="control-label" for="invoice_due_date">@lang('main.settings.field.invoice_due_date')</label>
                                 <input id="invoice_due_date" name="invoice_due_date" type="text" class="datepicker form-control styler" value="{{$settings->invoice_due_date}}" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="invoice_due_payment_by">@lang('main.settings.field.invoice_due_payment_by')</label>
                                 <input id="invoice_due_payment_by" name="invoice_due_payment_by" class="form-control styler" type="text" maxlength="200"  value="{{$settings->invoice_due_payment_by}}" />
                            </div>--}}

                            <div class="form-group">
                                <label class="control-label" for="message_title">Email @lang('main.settings.field.message_title')</label>
                                <input id="message_title" name="message_title" class="form-control styler" type="text" maxlength="200"  value="{{$settings->message_title}}" />
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-heading card-default">
                                        Email Message Body
                                    </div>
                                    <div class="card-block editor-fit">
                                        <textarea class="editor1" name="default_message2" >{{$settings->default_message2}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-heading card-default">
                                        Invoice @lang('main.settings.field.default_message')
                                    </div>
                                    <div class="card-block editor-fit">
                                        <textarea class="editor1" name="default_message" >{{$settings->default_message}}</textarea>
                                    </div>
                                </div>
                            </div>
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

