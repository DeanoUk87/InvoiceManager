<?php
/*
* =======================================================================
* FILE NAME:        Admincomposer.php
* DATE CREATED:  	05-01-2019
* FOR TABLE:  		admin_composer
* AUTHOR:			DH Fusion Ltd.
* CONTACT:			http://dhfusion.co.uk <info@dhfusion.co.uk>
* =======================================================================
*/

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Admincomposer extends Model
{
    /*disable Eloquent timestamps*/
    public $timestamps = false;

    /*database table name*/
    protected $table = 'admin_composer';
    
    /*get primary key name as in DB*/
    protected $primaryKey = 'id';
    
    /*fillable fields*/
    protected $fillable = [
        'message_type','to','user_email','from','title','message','created_at','updated_at','message_by','document'
    ];
    
    /*//custom timestamps name
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    */
}