<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //				
    protected $fillable=['Service_Id','Service_Id','Visitor_Id','Price','Status','Target_Id'];

        //NEW Relations 

        public function Visitor()
        {
            return $this->hasOne(Visitor::class, 'id', 'Visitor_Id');
        }

        public function Service()
        {
            return $this->hasOne(Service::class, 'id', 'Service_Id');
        }

}