<?php

namespace App\Model\Theme;


use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection = "mongodb";
    protected $collection = "users";

/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',"_id"
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function allergens(){
        return  $this->belongsToMany(Nutrients::class, "allergen_user", "nutrient_id", "user_id");
    }
}
