<?php
/*
* =======================================================================
* FILE NAME:        SalesImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		sales
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;

use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SalesImports implements ToModel, WithBatchInserts, WithChunkReading
{
    protected $fileName;

    function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param array $row
     *
     * @return Sales|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Sales(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        $code = Str::random(10);
        return new Sales([
            'invoice_number' => $row[0],
            'invoice_date' => $row[1],
            'customer_account' => $row[2],
            'customer_name' => $row[3],
            'address1' => $row[4],
            'address2' => $row[5],
            'town' => $row[6],
            'country' => $row[7],
            'postcode' => $row[8],
            'spacer1' => $row[9],
            'customer_account2' => $row[10],
            'numb1' => $row[11],
            'items' => $row[12],
            'weight' => $row[13],
            'invoice_total' => $row[14],
            'numb2' => $row[15],
            'spacer2' => $row[16],
            'job_number' => $row[17],
            'job_date' => $row[18],
            'sending_deport' => $row[19],
            'delivery_deport' => $row[20],
            'destination' => $row[21],
            'town2' => $row[22],
            'postcode2' => $row[23],
            'service_type' => $row[24],
            'items2' => $row[25],
            'volume_weight' => $row[26],
            'numb3' => $row[27],
            'increased_liability_cover' => $row[28],
            'sub_total' => $row[29],
            'spacer3' => $row[30],
            'numb4' => $row[31],
            'sender_reference' => $row[32],
            'numb5' => $row[33],
            'percentage_fuel_surcharge' => $row[34],
            'percentage_resourcing_surcharge' => $row[35],
            'spacer4' => $row[36],
            'senders_postcode' => $row[37],
            'vat_amount' => $row[38],
            'vat_percent' => $row[39],
            'uploadcode' => $code,
            'job_dat' => Carbon::parse()->format('Y-m-d'),
            'upload_ts' => $this->fileName
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
