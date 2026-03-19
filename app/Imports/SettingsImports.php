<?php
/*
* =======================================================================
* FILE NAME:        SettingsImports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		settings
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Imports;
use App\Models\Settings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class SettingsImports implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Settings|null
     * To import data without header, remove WithHeadingRow from the class above
     * then use the format like this:  Settings(['name' => $row[0],...])
     */
    public function model(array $row)
    {
        return new Settings([
           'company_name' => $row['company_name'],
                'logo' => $row['logo'],
                'company_address1' => $row['company_address1'],
                'company_address2' => $row['company_address2'],
                'state' => $row['state'],
                'city' => $row['city'],
                'postcode' => $row['postcode'],
                'country' => $row['country'],
                'phone' => $row['phone'],
                'fax' => $row['fax'],
                'cemail' => $row['cemail'],
                'website' => $row['website'],
                'primary_contact' => $row['primary_contact'],
                'base_currency' => $row['base_currency'],
                'vat_number' => $row['vat_number'],
                'invoice_due_date' => $row['invoice_due_date'],
                'invoice_due_payment_by' => $row['invoice_due_payment_by'],
                'message_title' => $row['message_title'],
                'default_message' => $row['default_message'],
                'default_message2' => $row['default_message2'],
                'send_limit' => $row['send_limit'],
               
        ]);
    }
}
