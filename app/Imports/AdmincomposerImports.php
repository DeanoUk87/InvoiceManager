<?php
/*
* =======================================================================
* FILE NAME:        AdmincomposerImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		admin_composer
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;
use App\Models\Admincomposer;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class AdmincomposerImports implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Admincomposer|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Admincomposer(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        return new Admincomposer([
           'message_type' => $row['message_type'],
                'to' => $row['to'],
                'user_email' => $row['user_email'],
                'from' => $row['from'],
                'title' => $row['title'],
                'message' => $row['message'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'message_by' => $row['message_by'],
                'document' => $row['document'],
               
        ]);
    }
}
