<?php

namespace App\Model\Panel;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class edietfoods_file extends Model implements Authenticatable
{
    use Authenticabletrait;
    use Notifiable;
    protected $connection="mongodb";
    protected $collection = 'edietfoods_file';
    protected $primarykey = "_id";
    protected $casts = [
        'edietfoods_id' => 'string',
    ];

    public function edietfoods_file()
    {
        return $this->belongsTo(edietfoods::class);
    }
}
