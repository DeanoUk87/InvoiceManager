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

<body>

<htmlpageheader name="page-header">
    {{--<b>{{$setup->company_name}}</b>--}}
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <b><i>Page: {PAGENO}</i></b>
</htmlpagefooter>

<table width="100%" border="0" cellspacing="0">
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
            @if($setup->phone)<span style="font-size: 12px; font-weight: bold;">TEL:</span> {{$setup->phone}}<br />@endif
            @if($setup->vat_number)<span style="font-weight: bold; font-size: 12px;">VAT NUMBER:</span> {{$setup->vat_number}}@endif
        </td>
    </tr>
    <tr>
        <td colspan="3" style="border-bottom:#CCC 1px solid;">&nbsp;</td>
    </tr>

    <tr>
        <td style="padding:0 10px 0;">
            <span style="font-weight: bold;">{{strtoupper($prof->customer_name)}}</span><br />
            @if($prof->address1){{$prof->address1}}<br />@endif
            @if($prof->address2){{$prof->address2}}<br />@endif
            @if($prof->postcode){{$prof->postcode}}<br />@endif
        </td>
        <td valign="top">&nbsp;</td>
        <td valign="top" style="padding:10px; text-align: right" >
            <span style="font-family: Verdana, Geneva, sans-serif; font-weight: bold;">ACCOUNT:</span> {{$prof->customer_account}}<br />
            <span style="font-weight: bold; font-size: 12px;">INVOICE NO:</span> {{$prof->invoice_number}}<br />
            <span style="font-weight: bold; font-size: 12px;">INVOICE DATE:</span> {{\Carbon\Carbon::parse($prof->invoice_date,config('timezone'))->format('d-m-Y')}}<br />
            @if($owner->po_number)<span style="font-weight: bold;"><span style="font-size: 12px">PO NUMBER:</span> {{$owner->po_number}}</span>@endif
        </td>
    </tr>
</table>


<table width="100%" border="0" cellspacing="0">

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
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding: 10px;">
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
                                //$total= $net_total+$vat;
                            @endphp
                            <tr>
                                <td width="64%" align="right"><strong style="font-size: 12px">SUB TOTAL:</strong></td>
                                <td align="right">{{number_format($newSubtotal,2)}}</td>
                            </tr>
                            <tr>
                                <td align="right" ><strong style="font-size: 12px; font-weight: bold;">FUEL SURCHARGE {{$prof->percentage_fuel_surcharge}}%:</strong></td>
                                <td align="right">{{number_format($fuel_surcharge,2)}}</td>
                            </tr>
                            <tr>
                                <td align="right"><strong style="font-size: 12px">NET TOTAL:</strong></td>
                                <td align="right">{{number_format($net_total,2)}}</td>
                            </tr>
                            <tr>
                                <td align="right"><strong style="font-size: 12px">VAT (20%):</strong></td>
                                <td align="right">{{number_format($vat,2)}}</td>
                            </tr>
                            <tr style="border-top:#CCC 1px solid; border-bottom:#CCC 1px solid; padding:10px;">
                                <td align="right"><strong style="color: #ff0000; font-size: 13px;">TOTAL:</strong></td>
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
                    <td colspan="3"><strong>INVOICE DUE PAYMENT BY: </strong><br />{{\Carbon\Carbon::parse(config('timezone',$prof->invoice_date))->addDays($prof->numb2)->format('dS M Y')}}</td>
                </tr>
                @if($customer->terms_of_payment){
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

</body>


