<?php
/*
* =======================================================================
* FILE NAME:        InvoicesExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		invoices
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Invoices;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExports implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        return Invoices::all(['sales_id','customer_account','invoice_number','invoice_date','due_date','date_created','terms','printer','po_number','num','email_status']);
    }

    public function headings(): array
    {
        /* Remove header*/
        //return [];
        
        /* Enable headers */
        return ['sales_id','customer_account','invoice_number','invoice_date','due_date','date_created','terms','printer','po_number','num','email_status'];
    }
}
