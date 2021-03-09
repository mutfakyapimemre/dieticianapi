<?php
namespace App\Model\Panel;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model ;
use Illuminate\Auth\Authenticatable as Authenticabletrait;
use Illuminate\Contracts\Auth\Authenticatable;

class edietfoods extends Model implements Authenticatable
{

    use Authenticabletrait;
    use Notifiable;
    protected $collection = 'edietfoods';
    protected $primarykey = "_id";
    protected $guarded = [];
    public function edietfoods()
    {
        return $this->hasOne(edietfoods_file::class, 'edietfoods_id', "_id")->select("img_url","edietfoods_id")->where(["isCover"=>1]);
    }
}
