
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.invoices.title')
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

    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
        body,td,th {
            font-family: Verdana, Geneva, sans-serif;
            color: #000;
            font-size:12px;

        }
        body {
            background-color: #FFF;
            font-family: Verdana, Geneva, sans-serif;
        }

        .table {
            border-collapse: collapse;
            border: 1px solid #E9E9E9;

        }
        .table td, .table th {
            border: 1px solid #E9E9E9;
        }
        .table tr:first-child th {
            border-top: 0;
        }
        .table tr:last-child td {
            border-bottom: 1px;
        }
        .table tr td:first-child,
        .table tr th:first-child {
            border-left: 1px;
        }
        .table tr td:last-child,
        .table tr th:last-child {
            border-right: 1px;
        }

    </style>
    <div class="row mb-2 viewDetails">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">Invoice</a>
                        <div class="btn-group">
                            <a href="{{\Illuminate\Support\Facades\URL::previous()}}" class="btn btn-info btn-sm"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                            <a href="{{route('archive.invoices.pdfdetails',['account'=>$customer_account,'invno'=>$invoice_number,'date'=>$invoice_date])}}" class="btn btn-success btn-sm" ><i class="fa fa-file-pdf"></i> @lang('app.pdf')</a>
                            <a href="{{route('archive.invoices.exceldetails',['account'=>$customer_account,'invno'=>$invoice_number,'date'=>$invoice_date])}}" class="btn btn-danger btn-sm" ><i class="fa fa-file-excel-o"></i> Excel</a>
                            @if(Auth::user()->hasAnyRole(['admin','admin2']))
                                <a href="#" title="Email" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-print"></i> Email</a>
                            @endif
                        </div>
                    </nav>
                </div>
                <div class="card-body">
                    <table style="width: 100%">
                        <tr>
                            <td width="43%" valign="top" style="padding:10px;">
                                @if(file_exists(base_path('public/uploads').'/'.$setup->logo))
                                    <img class="file-width" src="{{ asset('public/uploads')}}/{{$setup->logo}}" alt="logo" style="max-width:195px;">
                                @endif
                            </td>
                            <td width="20%">&nbsp;</td>
                            <td width="37%" style="padding:10px 10px 0px 10px; text-align: right ">
                                <span style="font-weight: bold;">{{$setup->company_name}}</span><br />
                                @if($setup->company_address1){{$setup->company_address1}}<br />@endif
                                @if($setup->company_address2){{$setup->company_address2}}<br />@endif
                                @if($setup->postcode){{$setup->postcode}}<br />@endif
                                @if($setup->phone)<span style="font-size: 12px; font-weight: bold;">TEL:</span> {{$setup->phone}}<br />
                                @endif
                                @if($setup->vat_number)<span style="font-weight: bold; font-size: 12px;">VAT NUMBER:</span> {{$setup->vat_number}}@endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border-bottom:#CCC 1px solid;">&nbsp;</td>
                        </tr>

                        <tr>
                            <td style="padding:0 10px 0;">
                                <span style="font-weight: bold;">@if(isset($prof->customer_name)){{strtoupper($prof->customer_name)}}@endif</span><br />
                                @if($prof->address1){{$prof->address1}}<br />@endif
                                @if($prof->address2){{$prof->address2}}<br />@endif
                                @if($prof->postcode){{$prof->postcode}}<br />@endif
                            </td>
                            <td valign="top">&nbsp;</td>
                            <td valign="top" style="padding:10px; text-align: right" >
                                <span style="font-size: 12px; font-weight: bold;">ACCOUNT:</span> {{$prof->customer_account}}<br />
                                <span style="font-weight: bold; font-size: 12px;">INVOICE NO:</span> {{$prof->invoice_number}}<br />
                                <span style="font-size: 12px; font-weight: bold;">INVOICE DATE:</span> {{\Carbon\Carbon::parse($prof->invoice_date,config('timezone'))->format('d-m-Y')}}<br />
                                <span style="font-weight: bold;"><span style="font-size: 12px">PO NUMBER:</span> @if($owner){{$owner->po_number}}@endif</span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                <table  class="table table-bordered" width="100%" border="0" cellspacing="0" style="margin-top:15px;">
                                    <tr style="font-weight:bold; background-color:#E9E9E9;">
                                        {{--<td>ID</td>--}}
                                        <td>JOB DATE</td>
                                        <td>JOB NUMBER</td>
                                        <td>SENDERS REF</td>
                                        <td>POSTCODE</td>
                                        <td>DESTINATION</td>
                                        <td>SERVICE TYPE</td>
                                        <td>ITEMS</td>
                                        <td>WEIGHT</td>
                                        <td>CHARGE</td>
                                        {{--<td>INVOICE TOTAL</td>--}}
                                    </tr>
                                    @php
                                        $newSubtotal=0;
                                    @endphp
                                    @foreach($items as $key => $item)
                                        @php
                                            $newSubtotal+=$item->sub_total;
                                        @endphp
                                        <tr>
                                            {{--<td>{{$key+1}}</td>--}}
                                            <td>{{\Carbon\Carbon::parse(config('timezone',$item->job_date))->format('d-m-Y')}}</td>
                                            <td>{{$item->job_number}}</td>
                                            <td>{{$item->sender_reference}}</td>
                                            <td>{{$item->postcode2}}</td>
                                            <td>{{$item->destination}}</td>
                                            <td>{{$item->service_type}}</td>
                                            <td>{{$item->items2}}</td>
                                            <td>{{number_format($item->volume_weight,2)}}</td>
                                            <td>{{number_format($item->sub_total,2)}}</td>
                                            {{--<td>{{number_format($item->invoice_total,2)}}</td>--}}
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding: 10px; font-size: 12px; font-weight: bold;">
                                    <tr>
                                        <td width="38%">&nbsp;</td>
                                        <td width="26%" align="right" valign="top">
                                        </td>
                                        <td width="36%" align="right" >
                                            <table class="table table-bordered" width="100%" border="0" cellspacing="0" >
                                                @php
                                                    $fuel_surcharge=$newSubtotal*$prof->percentage_fuel_surcharge/100;
                                                    $net_total= $newSubtotal+$fuel_surcharge;
                                                    $vat= $prof->vat_amount;
                                                @endphp
                                                <tr>
                                                    <td width="64%" align="right"><strong>SUB TOTAL:</strong></td>
                                                    <td align="right">{{number_format($newSubtotal,2)}}</td>
                                                </tr>
                                                <tr>
                                                    <td align="right" ><strong>FUEL SURCHARGE {{$prof->percentage_fuel_surcharge}}%:</strong></td>
                                                    <td align="right">{{number_format($fuel_surcharge,2)}}</td>
                                                </tr>
                                                <tr>
                                                    <td align="right"><strong>NET TOTAL:</strong></td>
                                                    <td align="right">{{number_format($net_total,2)}}</td>
                                                </tr>
                                                <tr>
                                                    <td align="right"><strong>VAT (20%):</strong></td>
                                                    <td align="right">{{number_format($vat,2)}}</td>
                                                </tr>
                                                <tr style="border-top:#CCC 1px solid; border-bottom:#CCC 1px solid; padding:10px;">
                                                    <td align="right"><strong style="color:#ff0000;">TOTAL:</strong></td>
                                                    <td align="right"><strong style="color:#ff0000;">{!! $setup->base_currency !!} {{number_format(( $net_total+$vat),2)}}</strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="table">
                                    <tr>
                                        <td>
                                            @role('admin')
                                            <form action="#" method="post" name="hezecomform" id="hezecomform" enctype="multipart/form-data">

                                                <input name="customer_account" type="hidden" value="{{$customer_account}}" />
                                                <input name="invoice_date" type="hidden" value="{{$invoice_date}}" />
                                                <input name="invoice_id" type="hidden" value="{{$invoice_number}}" />

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label" for="due_date"><strong>INVOICE DUE PAYMENT BY:</strong> </label>
                                                            <input name="due_date" class="form-control styler" disabled="disabled" type="text" maxlength="11" value="{{\Carbon\Carbon::parse(config('timezone',$prof->invoice_date))->addDays($prof->numb2)->format('d-m-Y')}}" />
                                                        </div>

                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <textarea name="terms" rows="3" class="form-control" placeholder="NOTES or Terms of payment" style="height: 100px" disabled="disabled">{{$setup->default_message}}</textarea>
                                                        </div>
                                                    </div>
                                                    {{--<div class="col-md-3">
                                                        <div class="form-group">
                                                            <input type="submit" name="button" id="hButton" class="btn btn-success btn-lg" value="Update Invoice" />
                                                        </div>
                                                    </div>--}}

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <div id="output"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            @else
                                                <br />
                                                <table width="100%" border="0" cellpadding="10" cellspacing="0" class="table">
                                                    <tr>
                                                        <td><strong>INVOICE DUE PAYMENT BY: </strong><br />{{\Carbon\Carbon::parse(config('timezone',$prof->invoice_date))->addDays($prof->numb2)->format('d-m-Y')}}</td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong>NOTES: </strong><br />
                                                            <span style="text-align: center">{!! $setup->default_message !!}</span></td>
                                                    </tr>
                                                </table>
                                                @endrole
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    @if(Auth::user()->hasAnyRole(['admin','admin2']))
        <form action="{{route('archive.invoices.sendmail')}}" method="post" id="" name="hezecomform">
            {{ csrf_field() }}
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Email Invoice to @if(isset($prof->customer_name)){{strtoupper($prof->customer_name)}}@endif</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <input name="customer_account" type="hidden" value="{{$customer_account}}" />
                                <input name="invoice_date" type="hidden" value="{{$invoice_date}}" />
                                <input name="invoice_number" type="hidden" value="{{$invoice_number}}" />
                                <div class="form-group text-center">
                                    <input name="customer_email" class="form-control styler" type="text" value="@if($mail){{$mail->customer_email}}@endif" placeholder="enter email separated by comma(,)" />
                                    {{--<br><br>
                                    <a href="{{route('invoices.sendmail', ['account' => $customer_account, 'invno' =>$invoice_number, 'date' => $invoice_date])}}" class="btn btn-success btn-lg">
                                        Send this invoice to customer email
                                    </a>--}}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" name="button" id="hButton" class="btn btn-primary" value="Send Message" />
                            <div class="hts-flash"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection

