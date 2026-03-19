<?php
/*
* =======================================================================
* FILE NAME:        MessagesstatusImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		messages_status
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;
use App\Models\Messagesstatus;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class MessagesstatusImports implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Messagesstatus|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Messagesstatus(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        return new Messagesstatus([
           'message_id' => $row['message_id'],
                'customer_id' => $row['customer_id'],
                'sent_status' => $row['sent_status'],
                'sent_at' => $row['sent_at'],
               
        ]);
    }
}
