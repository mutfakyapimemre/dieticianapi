<?php

namespace App\Model\Theme;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class NutrientsFile extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection="mongodb";
    protected $collection = 'nutrients_file';
    protected $primarykey = "_id";
    protected $casts = [
        'nutrients_id' => 'string',
    ];

    public function nutrients_file()
    {
        return $this->belongsTo(Nutrients::class);
    }
}
