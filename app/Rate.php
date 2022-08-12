<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    //
	
    protected $fillable=['visitor_id','service_id','rate_value'];
}
