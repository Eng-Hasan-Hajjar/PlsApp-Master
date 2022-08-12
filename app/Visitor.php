<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Company;
class Visitor extends Authenticatable implements JWTSubject
{
    use Notifiable;

        
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


     protected $table="visitors";
     protected $fillable=[
    	'vis_name', 'vis_last_name', 'vis_phone', 'vis_city', 'vis_address','vis_password','role','email','vis_username','vis_Status','vis_restpass_token','vis_activation_token'
    ];

    protected $hidden=['vis_password'];

      public function companies()
    {
        return $this->belongsToMany(Company::class,'company_visitor');
    }

    public function getAuthPassword()
    {
        return $this->vis_password;
    }

}
