<?php
/*
* =======================================================================
* FILE NAME:        AdmincomposerExports.php
* DATE CREATED:  	09-09-2020
* FOR TABLE:  		admin_composer
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Exports;
use App\Models\Admincomposer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdmincomposerExports implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        return Admincomposer::all(['message_type','to','user_email','from','title','message','created_at','updated_at','message_by','document']);
    }

    public function headings(): array
    {
        /* Remove header*/
        //return [];
        
        /* Enable headers */
        return ['message_type','to','user_email','from','title','message','created_at','updated_at','message_by','document'];
    }
}
