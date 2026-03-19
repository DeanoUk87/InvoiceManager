<?php
/*
* =======================================================================
* FILE NAME:        CustomersImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		customers
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;
use App\Models\Customers;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImports implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Customers|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Customers(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        if(isset($row['user_id'])){
            $userId = $row['user_id'];
        }else{
            $userId = '';
        }
        if(isset($row['customer_email_bcc'])){
            $customer_email_bcc = $row['customer_email_bcc'];
        }else{
            $customer_email_bcc = '';
        }
        return new Customers([
            'user_id' => $userId,
            'customer_account' => $row['customer_account'],
            'customer_email' => $row['customer_email'],
            'customer_email_bcc' => $customer_email_bcc,
            'customer_phone' => $row['customer_phone'],
            'terms_of_payment' => $row['terms_of_payment'],
            'message_type' => $row['message_type'],
            'po_number' => $row['po_number'],

        ]);
    }
}
