@extends('layouts.form')
@section('content')

    <div class="row mb-2 viewDetails">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">@lang('main.settings.details')</a>
                        <div class="btn-group">
                            <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('usersettings.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                            <a href="{{route('usersettings.pdfdetails',['id'=>$settings->id])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>
                        </div>
                    </nav>
                </div>
                <div class="card-body">
                    <ul class="list-group">

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.company_name')</span>
                            <p>{{$settings->company_name}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.company_address1')</span>
                            <p>{{$settings->company_address1}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.company_address2')</span>
                            <p>{{$settings->company_address2}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.state')</span>
                            <p>{{$settings->state}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.city')</span>
                            <p>{{$settings->city}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.postcode')</span>
                            <p>{{$settings->postcode}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.country')</span>
                            <p>{{$settings->country}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.phone')</span>
                            <p>{{$settings->phone}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.fax')</span>
                            <p>{{$settings->fax}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.cemail')</span>
                            <p>{{$settings->cemail}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.website')</span>
                            <p>{{$settings->website}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.primary_contact')</span>
                            <p>{{$settings->primary_contact}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.base_currency')</span>
                            <p>{{$settings->base_currency}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.vat_number')</span>
                            <p>{{$settings->vat_number}}</p>
                        </li>

                        {{--<li class="list-group-item">
                            <span>@lang('main.settings.field.invoice_due_date')</span>
                            <p>{{$settings->invoice_due_date}}</p>
                        </li>--}}

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.invoice_due_payment_by')</span>
                            <p>{{$settings->invoice_due_payment_by}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.message_title')</span>
                            <p>{{$settings->message_title}}</p>
                        </li>

                        <li class="list-group-item">
                            <span>Message Body</span>
                            <p>{!!$settings->default_message2!!}</p>
                        </li>

                        <li class="list-group-item">
                            <span>@lang('main.settings.field.default_message')</span>
                            <p>{!!$settings->default_message!!}</p>
                        </li>


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
                    </ul>

                </div>
            </div>
        </div>
    </div>

@endsection
        
