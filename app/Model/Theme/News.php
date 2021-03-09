<?php

namespace App\Model\Theme;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class News extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection="mongodb";
    protected $collection = 'news';
    protected $hidden =["_id"];
    protected $guarded = ["_id"];
    protected $primarykey = "_id";
    protected $casts = [
        'doctors_id' => 'string',
    ];


    public  function doctors()
       {

           return $this->hasOne(Doctors::class, '_id', "doctors_id");
       }
}
