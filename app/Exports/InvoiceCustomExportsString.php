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
use App\Http\Controllers\Traits\CustomQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class InvoiceCustomExportsString implements FromArray, WithCustomCsvSettings
{

    use Exportable, CustomQuery;

    protected  $invoice;

    public function __construct(array $invoice)
    {
        $this->invoice = $invoice;
    }

    public function array(): array
    {
        return $this->invoice;
    }

    public function getCsvSettings(): array
    {
        return [
            'line_ending' => ''
        ];
    }
}
