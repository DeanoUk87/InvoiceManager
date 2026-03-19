@extends('layouts.form')
@section('content')

<div class="row mb-2 viewDetails">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('main.sales.details')</a>
                    <div class="btn-group">
                        <a href="javascript:viod(0)" class="btn btn-info btn-sm" onclick="viewAll('{{route('archive-sales.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                        {{--<a href="{{route('archive-sales.pdfdetails',['id'=>$sales->sales_id])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>--}}
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <ul class="list-group">

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.invoice_number')</span>
                        <p>{{$sales->invoice_number}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.invoice_date')</span>
                        <p>{{$sales->invoice_date}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.customer_account')</span>
                        <p>{{$sales->customer_account}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.customer_name')</span>
                        <p>{{$sales->customer_name}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.address1')</span>
                        <p>{{$sales->address1}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.address2')</span>
                        <p>{{$sales->address2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.town')</span>
                        <p>{{$sales->town}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.country')</span>
                        <p>{{$sales->country}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.postcode')</span>
                        <p>{{$sales->postcode}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.spacer1')</span>
                        <p>{{$sales->spacer1}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.customer_account2')</span>
                        <p>{{$sales->customer_account2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.numb1')</span>
                        <p>{{$sales->numb1}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.items')</span>
                        <p>{{$sales->items}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.weight')</span>
                        <p>{{$sales->weight}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.invoice_total')</span>
                        <p>{{$sales->invoice_total}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.numb2')</span>
                        <p>{{$sales->numb2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.spacer2')</span>
                        <p>{{$sales->spacer2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.job_number')</span>
                        <p>{{$sales->job_number}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.job_date')</span>
                        <p>{{$sales->job_date}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.sending_deport')</span>
                        <p>{{$sales->sending_deport}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.delivery_deport')</span>
                        <p>{{$sales->delivery_deport}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.destination')</span>
                        <p>{{$sales->destination}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.town2')</span>
                        <p>{{$sales->town2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.postcode2')</span>
                        <p>{{$sales->postcode2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.service_type')</span>
                        <p>{{$sales->service_type}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.items2')</span>
                        <p>{{$sales->items2}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.volume_weight')</span>
                        <p>{{$sales->volume_weight}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.numb3')</span>
                        <p>{{$sales->numb3}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.increased_liability_cover')</span>
                        <p>{{$sales->increased_liability_cover}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.sub_total')</span>
                        <p>{{$sales->sub_total}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.spacer3')</span>
                        <p>{{$sales->spacer3}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.numb4')</span>
                        <p>{{$sales->numb4}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.sender_reference')</span>
                        <p>{{$sales->sender_reference}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.numb5')</span>
                        <p>{{$sales->numb5}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.percentage_fuel_surcharge')</span>
                        <p>{{$sales->percentage_fuel_surcharge}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.spacer4')</span>
                        <p>{{$sales->spacer4}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.senders_postcode')</span>
                        <p>{{$sales->senders_postcode}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.vat_amount')</span>
                        <p>{{$sales->vat_amount}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.vat_percent')</span>
                        <p>{{$sales->vat_percent}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.uploadcode')</span>
                        <p>{{$sales->uploadcode}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.ms_created')</span>
                        <p>{{$sales->ms_created}}</p>
                    </li>

                    <li class="list-group-item">
                        <span>@lang('main.sales.field.job_dat')</span>
                        <p>{{$sales->job_dat}}</p>
                    </li>


                </ul>

            </div>
        </div>
    </div>
</div>

@endsection

