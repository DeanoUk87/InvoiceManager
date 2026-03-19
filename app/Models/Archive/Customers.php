<?php
/*
* =======================================================================
* FILE NAME:        Customers.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		customers
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/

namespace App\Models\Archive;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    /*Database connection*/
    protected $connection = 'mysql2';

    /*disable Eloquent timestamps*/
    public $timestamps = false;

    /*database table name*/
    protected $table = 'customers_profile';
    
    /*get primary key name as in DB*/
    protected $primaryKey = 'contact_id';
    
    /*fillable fields*/
    protected $fillable = ['user_id','customer_account','customer_email','customer_phone','terms_of_payment','message_type','po_number'];
    
    /*//custom timestamps name
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    */
}