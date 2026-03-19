<?php
/*
* =======================================================================
* FILE NAME:        InvoicesImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		invoices
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;
use App\Models\Invoices;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class InvoicesImports implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Invoices|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Invoices(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        return new Invoices([
           'sales_id' => $row['sales_id'],
                'customer_account' => $row['customer_account'],
                'invoice_number' => $row['invoice_number'],
                'invoice_date' => $row['invoice_date'],
                'due_date' => $row['due_date'],
                'date_created' => $row['date_created'],
                'terms' => $row['terms'],
                'printer' => $row['printer'],
                'po_number' => $row['po_number'],
                'num' => $row['num'],
                'email_status' => $row['email_status'],
               
        ]);
    }
}
