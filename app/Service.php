<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Company;

class Service extends Model
{
     protected $table="services";
     protected $fillable=[
    	'ser_name', 'ser_image', 'ser_description', 'ser_price',
    ];
        public function companies()
    {
        return $this->belongsToMany(Company::class,'company_services');
    }
        public function categories()
    {
        return $this->belongsTo(Category::class);
    }


    //New Relations

    
    public function Category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function Rates()
    {
        return $this->hasMany(Rate::class, 'service_id', 'id');
    }
    
    public function getRatesAvgAttribute()
    {
        return $this->Rates()->avg('rate_value') ?: 0;
        // return $this->hasMany(Rate::class, 'service_id', 'id')->avg('rate_value');
    }

}