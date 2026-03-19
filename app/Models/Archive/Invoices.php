<?php
/*
* =======================================================================
* FILE NAME:        Invoices.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		invoices
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/

namespace App\Models\Archive;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    /*Database connection*/
    protected $connection = 'mysql2';

    /*disable Eloquent timestamps*/
    public $timestamps = false;


    /*database table name*/
    protected $table = 'invoices';
    
    /*get primary key name as in DB*/
    protected $primaryKey = 'invoice_id';
    
    /*fillable fields*/
    protected $fillable = ['sales_id','customer_account','invoice_number','invoice_date','due_date','date_created','terms','printer','po_number','num'];
    
    /*//custom timestamps name
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    */
}