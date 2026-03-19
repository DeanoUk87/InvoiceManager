<?php
/**
 * Created by PhpStorm.
 * User: dhfusion
 * Date: 2/4/2018
 * Time: 9:52 PM
 */

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    //database table name
    protected $table = 'hts_uploads';

    //default timestamp
    public $timestamps = false;

    //fillable fields
    protected $fillable = ['fileId','relatedId', 'filename', 'tablekey'];
}