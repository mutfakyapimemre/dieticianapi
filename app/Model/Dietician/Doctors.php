<?php

namespace App\Model\Dietician;

use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class Dieticians extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection = "mongodb";
    protected $collection = 'dieticians';
    protected $guarded = [];
    protected $primarykey = "_id";
    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
