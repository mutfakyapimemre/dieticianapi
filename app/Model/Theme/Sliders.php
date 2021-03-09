<?php

namespace App\Model\Theme;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class Sliders extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $hidden =["_id"];
    protected $connection="mongodb";
}
