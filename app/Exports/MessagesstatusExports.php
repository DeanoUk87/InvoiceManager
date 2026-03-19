<?php
/*
* =======================================================================
* FILE NAME:        MessagesstatusExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		messages_status
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Messagesstatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MessagesstatusExports implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        return Messagesstatus::all(['message_id','customer_id','sent_status','sent_at']);
    }

    public function headings(): array
    {
        /* Remove header*/
        //return [];
        
        /* Enable headers */
        return ['message_id','customer_id','sent_status','sent_at'];
    }
}
