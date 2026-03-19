<?php
/*
* =======================================================================
* FILE NAME:        SettingsExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		settings
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Settings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SettingsExports implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        return Settings::all(['company_name','logo','company_address1','company_address2','state','city','postcode','country','phone','fax','cemail','website','primary_contact','base_currency','vat_number','invoice_due_date','invoice_due_payment_by','message_title','default_message','default_message2','send_limit']);
    }

    public function headings(): array
    {
        /* Remove header*/
        //return [];
        
        /* Enable headers */
        return ['company_name','logo','company_address1','company_address2','state','city','postcode','country','phone','fax','cemail','website','primary_contact','base_currency','vat_number','invoice_due_date','invoice_due_payment_by','message_title','default_message','default_message2','send_limit'];
    }
}
