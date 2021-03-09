<?php

namespace App\Http\Controllers\Api\Panel\Doctors;

use App\Http\Controllers\Controller;
use App\Model\Panel\Doctors;
use App\Model\Theme\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/*
 *@author Umut Can Güngörmüş <umutcangungormus@mutfakyapim.com>
 */

class indexController extends Controller
{
    public $status;

    public function __construct(Request $request)
    {
        $bearer = $request->header("Authorization");
        $bearer = str_replace("Bearer ", "", $bearer);
        $user = User::where("api_token", $bearer)
            ->first();
        if ($user) {
            $this->status = $user->status;
        }
    }

    public function index(Request $request)
    {
        if ($this->status != "admin") {
            return response()->json("Bu İşlem İçin Yetkili Değilsiniz", 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            $doctors = DB::table("doctors")->get();
            if ($doctors) {
                return response($doctors, 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response("Listelenecek Veri Bulunmamaktadır.", 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "password" => "required|confirmed|min:6",
            "name" => "required|min:3",
            "address" => "required",
            "about_us" => "required",
            "company_name" => "required",
            "email" => "required|email|unique:doctors",
            "phone" => "required|numeric",
        ]);
        $doctors = new Doctors;
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            $slug= Str::slug( $request->name,"-");
            $control=Doctors::where("name",$doctors->name)->count();
            if($control){
                $doctors->slug=$slug."-".$control;
            }else{
                $doctors->slug=$slug;
            }
            $rank=Doctors::count();
            $data["rank"]=$rank+1;
            $doctors->name = $request->name;
            $doctors->password = Hash::make($request->password);
            $doctors->email = $request->email;
            $doctors->phone = $request->phone;
            $doctors->address = $request->address;
            $doctors->img_url = $request->img_url;
            $doctors->about_us = $request->about_us;
            $doctors->company_name = $request->company_name;
            $doctors->rank=$rank;
        }
        $doctors->save();
        return response($doctors, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function edit($id)
    {
        if ($this->status != "admin") {
            return response()->json("Bu İşlem İçin Yetkili Değilsiniz", 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            $doctors = Doctors::where("_id", $id)
                ->first();
            if ($doctors) {
                return response()->json(["data" => $doctors], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response("Böyle Bir Kullanıcı Yoktur.", 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function update($id, Request $request)
    {
        if ($this->status != "admin") {
            return response()->json("Bu İşlem İçin Yetkili Değilsiniz", 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            if ($id) {
                $doctors = Doctors::where("_id", $id)->first();
                if ($doctors) {
                    $data = $request->except("_token");
                    if (!empty($data["password"])) {
                        $validator = Validator::make($request->all(), [
                            'password' => 'required|confirmed|min:6'
                        ]);
                        if ($validator->fails()) {
                            return response()->json($validator->messages(), 200, [], JSON_UNESCAPED_UNICODE);
                        } else {
                            unset($data["password_confirmation"]);
                            $data["password"] = Hash::make($data["password"]);
                        }
                    }
                    if (!empty($data["id"])) {
                        unset($data["id"]);
                    }
                    if ($request->file()) {
                        $photo = $request->file("img_url");
                        $path = $request->img_url->path();
                        $extension = $request->img_url->extension();
                        $fileNameWithExtension = $photo->getClientOriginalName();
                        $name = Str::slug($data["name"], "-");
                        $fileNameWithExtension = $name . "-" . rand(0, 99999999999) . "-" . time() . "." . $extension;
                        $path = $request->img_url->storeAs("uploads/doctors/{$name}", $fileNameWithExtension, "public");
                        if(!empty($path)){
                            Storage::delete("public/".$doctors->img_url);
                        }
                        $data["img_url"] = $path;
                    }

                    $update = Doctors::where("_id", $id)
                        ->update($data);
                    if ($update) {
                        return response()->json(["success" => true, "title" => "Başarılı!", "msg" => "Güncelleme İşlemi Başarılı"], 200, [], JSON_UNESCAPED_UNICODE);
                    } else {
                        return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Güncelleme İşlemi Başarısız"], 200, [], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Böyle Bir Kullanıcı Bulunmamaktadır"], 200, [], JSON_UNESCAPED_UNICODE);
                }

            }
        }
    }

    public function destroy($id)
    {
        if ($this->status != "admin") {
            return response()->json("Bu İşlem İçin Yetkili Değilsiniz", 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            $delete = User::where("_id", $id)
                ->delete();
            if ($delete) {
                return response()->json("Silme İşlemi Başarılı", 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json("Silme İşlemi Başarısız Böyle Bir Kayıt Bulunamdı", 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
    }

}
