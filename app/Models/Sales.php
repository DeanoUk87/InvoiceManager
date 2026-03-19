<?php
/*
* =======================================================================
* FILE NAME:        Sales.php
* DATE CREATED:  	17-01-2019
* FOR TABLE:  		sales
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    /*disable Eloquent timestamps*/
    public $timestamps = false;

    /*database table name*/
    protected $table = 'sales';

    /*get primary key name as in DB*/
    protected $primaryKey = 'sales_id';

    /*fillable fields*/
    protected $fillable = [
        'invoice_number','invoice_date','customer_account','customer_name','address1','address2',
        'town','country','postcode','spacer1','customer_account2','numb1','items','weight',
        'invoice_total','numb2','spacer2','job_number','job_date','sending_deport','delivery_deport',
        'destination','town2','postcode2','service_type','items2','volume_weight','numb3',
        'increased_liability_cover','sub_total','spacer3','numb4','sender_reference','numb5',
        'percentage_fuel_surcharge','percentage_resourcing_surcharge','spacer4','senders_postcode',
        'vat_amount','vat_percent','uploadcode','ms_created','job_dat','upload_ts'
    ];

}
