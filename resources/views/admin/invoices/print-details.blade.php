<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{$setup->company_name}}</title>

    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
        body{
            line-height: 10px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 0px;
            border: 0px solid #eee;
            /*box-shadow: 0 0 10px rgba(0, 0, 0, .15);*/
            font-size: 12px;
            line-height: 24px;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
            color: #555;
            line-height: 15px;
        }

        .invoice-box table {
            width: 100%;
            line-height: 15px;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
            line-height: 15px
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 0px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 8px;
            border-collapse: collapse;
            white-space: nowrap;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
            font-size: 9px;
            border-collapse: collapse;
            white-space: nowrap;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-total td {
            padding: 0;
            margin: 0;
            border-collapse: collapse;
        }
    </style>
</head>

<body>

<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="9">
                <table>
                    <tr>
                        <td style="padding-right: 30px">
                            @if(file_exists(base_path('public/uploads').'/'.$setup->logo))
                                <img class="file-width" src="{{ asset('public/uploads')}}/{{$setup->logo}}" alt="logo" style="max-width:195px;">
                            @endif
                        </td>

                        <td style="font-size: 12px; font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', 'DejaVu Sans', Verdana, sans-serif; text-align: right;">
                            <span style="font-weight: bold;">{{$setup->company_name}}</span><br />
                            @if($setup->company_address1){{$setup->company_address1}}<br />@endif
                            @if($setup->company_address2){{$setup->company_address2}}<br />@endif
							@if($setup->state){{$setup->state}}<br />@endif
							@if($setup->city){{$setup->city}}<br />@endif
                            @if($setup->postcode){{$setup->postcode}}<br />@endif
                            @if($setup->phone)<span style="font-weight: bold; font-size: 12px;">Tel:</span> {{$setup->phone}}<br />@endif
                            @if($setup->vat_number)<span style="font-weight: bold; font-size: 12px;">Vat Number:</span> {{$setup->vat_number}}@endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="9">
                <table>
                    <tr>
                        <td style="font-size: 12px;">
                            <span style="font-weight: bold;">{{strtoupper($prof->customer_name)}}</span><br />
                            @if($prof->address1){{$prof->address1}}<br />@endif
                            @if($prof->address2){{$prof->address2}}<br />@endif
							@if($prof->town){{$prof->town}}<br />@endif
                            @if($prof->postcode){{$prof->postcode}}<br />@endif
                        </td>

                        <td style="text-align: right">
                            <span style="font-weight: bold; font-size: 12px;">Account:</span> {{$prof->customer_account}}<br />
                            <span style="font-weight: bold;"><span style="font-size: 12px">Invoice No</span>:</span> {{$prof->invoice_number}}<br />
                            <span style="font-weight: bold; font-size: 12px;">Invoice Date:</span> {{\Carbon\Carbon::parse($prof->invoice_date,config('timezone'))->format('d-m-Y')}}<br />
                            <span style="font-weight: bold;">PO Number: @if($owner){{$owner->po_number}}@endif</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
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
            <tr class="item">
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
        {{--<tr class="item last">
            <td colspan="9">
                -
            </td>
        </tr>--}}

        <tr>
            <td colspan="9" style="text-align: right">
                <table>
                    <tr>
                        <td width="38%">&nbsp;</td>
                        <td width="26%" align="right" valign="top">
                        </td>
                        <td width="56%" align="right" style="padding-top:10px;">
                            <table style="font-size: 12px;" class="invoice-total">
                                @php
                                    $fuel_surcharge=$newSubtotal*$prof->percentage_fuel_surcharge/100;
                                    $percentage_resourcing_surcharge=$newSubtotal*$prof->percentage_resourcing_surcharge/100;
                                    $net_total= $newSubtotal+$fuel_surcharge+$percentage_resourcing_surcharge;
                                    $vat= $prof->vat_amount;
                                @endphp
                                <tr>
                                    <td width="64%" align="right"><strong style="font-size: 12px;">Sub Total:</strong></td>
                                    <td align="right">{{number_format($newSubtotal,2)}}</td>
                                </tr>
                                <tr>
                                    <td align="right" ><strong style="font-size: 12px">Fuel Surcharge {{$prof->percentage_fuel_surcharge}}%:</strong></td>
                                    <td align="right">{{number_format($fuel_surcharge,2)}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="70%"><strong style="font-size: 12px">Percentage Resourcing Surcharge {{$prof->percentage_resourcing_surcharge}}%:</strong></td>
                                    <td align="right">{{number_format($percentage_resourcing_surcharge,2)}}</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong style="font-size: 12px">Net Total:</strong></td>
                                    <td align="right">{{number_format($net_total,2)}}</td>
                                </tr>
                                <tr>
                                    <td align="right"><strong style="font-size: 12px">Vat (20%):</strong></td>
                                    <td align="right">{{number_format($vat,2)}}</td>
                                </tr>
                                <tr style="border-top:#CCC 1px solid; border-bottom:#CCC 1px solid; padding:10px;">
                                    <td align="right"><strong style="color: #00773b; font-size: 13px;">Total:</strong></td>
                                    <td align="right"><strong style="color:#00773b;">{!! $setup->base_currency !!} {{number_format(( $net_total+$vat),2)}}</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="9">
                <table>
                    <tr>
                        <td colspan="3"><span style="font-size: 12px"><strong>Invoice Due Payment By</strong></span><strong>: </strong><br />{{\Carbon\Carbon::parse(config('timezone',$prof->invoice_date))->addDays($prof->numb2)->format('d-m-Y')}}</td>
                    </tr>
                    @if($customer){
                    <tr>
                        <td colspan="3" align="center" valign="top" style="font-size:12px color:#0067AA;">{!! $customer->terms_of_payment !!}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" valign="top" style="font-size: 12px; text-align: center;">{!! $setup->default_message !!}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<script type="text/php">
        if ( isset($pdf) ) {
            $x = 32;
            $y = 18;
            $text = "{PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "bold");
            $size = 8;
            $color = array(255,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
