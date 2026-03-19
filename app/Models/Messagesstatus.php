<?php
/*
* =======================================================================
* FILE NAME:        Messagesstatus.php
* DATE CREATED:  	17-04-2019
* FOR TABLE:  		messages_status
* AUTHOR:			Hezecom Technology Solutions LTD.
* CONTACT:			http://hezecom.com <info@hezecom.com>
* =======================================================================
*/

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Messagesstatus extends Model
{
    /*disable Eloquent timestamps*/
    public $timestamps = false;

    /*database table name*/
    protected $table = 'messages_status';
    
    /*get primary key name as in DB*/
    protected $primaryKey = 'id';
    
    /*fillable fields*/
    protected $fillable = ['message_id','customer_id','sent_status','sent_at'];
    
    /*//custom timestamps name
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    */
}