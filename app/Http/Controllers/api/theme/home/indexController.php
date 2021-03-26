<?php

namespace App\Http\Controllers\Api\Theme\Home;

use App\Http\Controllers\Controller;
use App\Model\Theme\Corporate;
use App\Model\Theme\Doctors;
use App\Model\Theme\FoodDecided;
use App\Model\Theme\News;
use App\Model\Theme\Settings;
use App\Model\Theme\Sliders;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class indexController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $viewData = "";

    public function __construct()
    {
        $this->viewData = new \stdClass();
        $this->viewData->menus = new \stdClass();
        $this->viewData->menus->corporate = Corporate::where("isActive", 1)->get(["title", "seo_url"]);
        $this->viewData->menus->food_decides = FoodDecided::where("isActive", 1)->get(["title", "seo_url"]);
        $this->viewData->settings = Settings::where("isActive", 1)->orderBy("rank")->limit(1)->first();
        $this->viewData->baseURL = urlencode(url("/"));

    }

    public function index()
    {
        $this->viewData->sliders = Sliders::where("isActive", 1)->get();
        $dcount = Doctors::count();
        $start = rand(0, $dcount - 8);
        $doctors= Doctors::where("isActive", 1)->skip($start)->take(8)->get();
        $doctors->makeHidden(["api_token","updated_at","isActive"]);
        $this->viewData->doctors = $doctors;
        foreach ($this->viewData->doctors as $doctor) {
            $this->viewData->doctors->profile_photo = $doctor->profilePhoto;
            /*
             * if (isset($doctor->profilePhoto) && !empty($doctor->profilePhoto)) {
                $this->viewData->doctors->profile_photo = $doctor->profilePhoto;
            } else {
                $this->viewData->doctors->profile_photo->img_url = "";
            }*/
        }

        $this->viewData->news = News::where("isActive", 1)->orderByDesc("rank")->limit(8)->get();
        foreach ($this->viewData->news as $news) {
            $this->viewData->news->doctors = $news->doctors;
        }

        return response()->json(["data" => $this->viewData], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function search(Request $request)
    {
        $per_page = (!empty($request->per_page) ? (int)$request->per_page : 12);
        if (!empty($request->table) && !empty($request->column)):
            foreach ($request->table as $key => $item):
                $response[$request->table] = DB::table($request->table)
                    ->where(["isActive" => 1])
                    ->where(function ($query) use ($request, $key) {
                        $query->where($request->column[$key], "like", "%" . Str::strto("lower", $request->search) . "%")
                            ->orWhere($request->column[$key], "like", "%" . Str::strto("lower|ucfirst", $request->search) . "%")
                            ->orWhere($request->column[$key], "like", "%" . Str::strto("lower|ucwords", $request->search) . "%")
                            ->orWhere($request->column[$key], "like", "%" . Str::strto("lower|upper", $request->search) . "%")
                            ->orWhere($request->column[$key], "like", "%" . Str::strto("lower|capitalizefirst", $request->search) . "%");
                    })
                    ->paginate($per_page);
            endforeach;
        endif;
        return response()->json(["data" => $response], 200, [], JSON_UNESCAPED_UNICODE);
    }
}

