<?php
/*
* =======================================================================
* FILE NAME:        CustomersExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		customers
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Customers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CustomersExports implements FromCollection, WithHeadings, WithCustomCsvSettings
{
    use Exportable;

    public function collection()
    {
        return Customers::all(['user_id','customer_account','customer_email','customer_email_bcc','customer_phone','terms_of_payment','message_type','po_number']);
    }

    public function headings(): array
    {
        /* Remove header */
        //return [];

        /* Enable headers */
        return ['user_id','customer_account','customer_email','customer_email_bcc','customer_phone','terms_of_payment','message_type','po_number'];
    }

    public function getCsvSettings(): array
    {
        return [
            'line_ending' => ';',
             'include_separator_line' => false,
             'excel_compatibility'    => false,
        ];
    }
}
