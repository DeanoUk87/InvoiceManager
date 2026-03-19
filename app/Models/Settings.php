<?php
/*
* =======================================================================
* FILE NAME:        Settings.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		settings
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /*disable Eloquent timestamps*/
    public $timestamps = false;

    /*database table name*/
    protected $table = 'settings';
    
    /*get primary key name as in DB*/
    protected $primaryKey = 'id';
    
    /*fillable fields*/
    protected $fillable = ['company_name','logo','company_address1','company_address2','state','city','postcode','country','phone','fax','cemail','website','primary_contact','base_currency','vat_number','invoice_due_date','invoice_due_payment_by','message_title','default_message','default_message2'];
    
    /*//custom timestamps name
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    */
}