<?php
/*
* =======================================================================
* FILE NAME:        SalesExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		sales
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Sales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExports implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        return Sales::all(['invoice_number','invoice_date','customer_account','customer_name','address1','address2','town','country','postcode','spacer1','customer_account2','numb1','items','weight','invoice_total','numb2','spacer2','job_number','job_date','sending_deport','delivery_deport','destination','town2','postcode2','service_type','items2','volume_weight','numb3','increased_liability_cover','sub_total','spacer3','numb4','sender_reference','numb5','percentage_fuel_surcharge','spacer4','senders_postcode','vat_amount','vat_percent','uploadcode','ms_created','job_dat']);
    }

    public function headings(): array
    {
        /* Remove header*/
        //return [];

        /* Enable headers */
        return ['invoice_number','invoice_date','customer_account','customer_name','address1','address2','town','country','postcode','spacer1','customer_account2','numb1','items','weight','invoice_total','numb2','spacer2','job_number','job_date','sending_deport','delivery_deport','destination','town2','postcode2','service_type','items2','volume_weight','numb3','increased_liability_cover','sub_total','spacer3','numb4','sender_reference','numb5','percentage_fuel_surcharge','spacer4','senders_postcode','vat_amount','vat_percent','uploadcode','ms_created','job_dat'];
    }
}
