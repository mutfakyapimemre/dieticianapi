<?php

namespace App\Model\Panel;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class recipes_file extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection="mongodb";
    protected $collection = 'recipes_file';
    protected $primarykey = "_id";
    protected $casts = [
        'recipes_id' => 'string',
    ];

    public function nutrients_file()
    {
        return $this->belongsTo(Nutrients::class);
    }
}
