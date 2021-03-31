<?php

namespace App\Http\Controllers\Api\Theme\Dieticians;

use App\Http\Controllers\Controller;
use App\Model\Theme\Corporate;
use App\Model\Theme\FoodDecided;
use App\Model\Theme\News;
use App\Model\Theme\Nutrients;
use App\Model\Theme\Settings;
use App\Model\Theme\Sliders;
use App\Model\Theme\User;
use App\Model\Theme\Dieticians;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Integer;

class indexController extends Controller
{

    public function __construct()
    {
        $this->viewData = new \stdClass();
        $this->viewData->menus = new \stdClass();
        $this->viewData->menus->corporate = Corporate::where("isActive", 1)->get(["title", "seo_url"]);
        $this->viewData->menus->food_decides = FoodDecided::where("isActive", 1)->get(["title", "seo_url"]);
        $this->viewData->settings = Settings::where("isActive", 1)->orderBy("rank")->first();
        $this->viewData->baseURL = urlencode(url("/"));
    }

    public function index(Request $request)
    {
        $per_page = empty($request->per_page) ? 12 : (int)$request->per_page;
        if (!empty($request->search)) :
            $search = $request->search;

            $this->viewData->dieticians = Dieticians::where(["isActive" => 1])
                ->where(function ($query) use ($search) {
                    $query->where("name", "like", "%" . Str::strto("lower", $search) . "%")
                        ->orWhere("name", "like", "%" . Str::strto("lower|ucfirst", $search) . "%")
                        ->orWhere("name", "like", "%" . Str::strto("lower|ucwords", $search) . "%")
                        ->orWhere("name", "like", "%" . Str::strto("lower|upper", $search) . "%")
                        ->orWhere("name", "like", "%" . Str::strto("lower|capitalizefirst", $search) . "%");
                })
                ->paginate($per_page);
        else :
            $this->viewData->dieticians = Dieticians::where("isActive", 1)->paginate($per_page);
        endif;
        foreach ($this->viewData->dieticians as $dietician) {
            if (!empty($dietician->profilePhoto)) {
                $this->viewData->dieticians->profile_photo = $dietician->profilePhoto;
            }
        }
        return response()->json(["data" => $this->viewData], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
