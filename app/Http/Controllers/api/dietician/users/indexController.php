<?php

namespace App\Http\Controllers\Api\Dietician\Users;

use App\Http\Controllers\Controller;
use App\Jobs\Panel\MailJobs;
use App\Model\Panel\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Model\Theme\Settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Model\Panel\Diseases;


class indexController extends Controller
{
    public $status = "";

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

    public function userBirthCalc(Request $request)
    {
        $dateOfBirth = $request->dates;
        $yearsOrmonth = Carbon::parse($dateOfBirth)->age;
        if ($yearsOrmonth > 0)
        {
            $years = Carbon::parse($dateOfBirth)->age;

        }else
        {
            $years = Carbon::parse($dateOfBirth)->diff(Carbon::now())->format('%m months');
        }
        return response($years,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {
        $data = $request->except("_token");
        //$data["password"] = Hash::make($data["password"]);
        $users = DB::table("users")
            ->insert($data);
      //dd($users);
       return response($users, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function edit($id)
    {

        $users = DB::table("users")
            ->where("_id", $id)
            ->first();
        if ($users) {
            return response()->json(["data" => $users], 200);
        } else {
            return response("Böyle Bir Kullanıcı Yoktur.", 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getUser(Request $request)
    {

         $users = User::where('tckn', $request->tc)//where("tc", $request->tc)
            //->where("phone", $request->phone)
            ->first();


       // print_r($users);
        if ($users) {
            return response()->json(["data" => $users], 200);
        } else {
            return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Bu Bilgilere Ait Bir Kullanıcı Bulunmamaktadır."], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function userMail(Request $request)
    {
        $users = DB::table("users")
            ->where("tc", $request->tc)
            ->where("phone", $request->phone)
            ->first();

        $dietician = DB::table("dieticians")
            ->where("_id", $request->dietician_id)
            ->first();
        if (!empty($users) && !empty($dietician)) {
            $key = rand(100000, 999999);
            $update = Db::table("users")->where("tc", $request->tc)
                ->where("phone", $request->phone)->update(["dietician_check" => $key]);
            $settings = Settings::where("isActive", 1)->first();


            $data = [
                'name' => $users["name"],
                'mail' => $users["email"],
                "headers" => $request->header("referer"),
                "host" => $request->header("host"),
                "dietician" => $dietician,
                "key" => $key,
                "logo" => asset("storage/" . $settings->logo)
            ];
            $mail = MailJobs::dispatch($data, $users);

            if ($mail) {
                return response()->json(["success" => true, "title" => "Başarılı!", "msg" => "Danışan Başvurunuz Başarıyla Danışana Bildirildi."], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "İsteğiniz İletilemedi."], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Bu Bilgilere Ait Bir Kullanıcı Bulunmamaktadır.", "data" => $users], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function userDiseases(Request $request)
    {


        $users = DB::table("users")
            ->where("tc", $request->tc)
            ->where("phone", $request->phone)
            ->first();

        $dietician = DB::table("dieticians")
            ->where("_id", $request->dietician_id)
            ->first();

        $data = [
          "ditican_id" => $dietician,
            "user_id" =>  $users->id,

        ];
        foreach ($request->diseaseName as $key => $disease) {
            $add_data["title"] = $disease;
        }

        $dataInsert = DB::table("users_diseases")
            ->insert($data)
            ->insert($add_data);

        if ($dataInsert) {
            return response()->json(["data" => $data,"data" => $add_data], 200);
        } else {
            return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Bu Bilgilere Ait Bir Kullanıcı Bulunmamaktadır."], 200, [], JSON_UNESCAPED_UNICODE);
        }

    }

    public function getAllDiseases()
    {
        $data["diseases"] = DB::table("diseases")->where("isActive", 1)->get();
        //dd($data["diseases"]);
        return response()->json(["data" => $data], 200);
    }

    public function dietMeal(Request $request)
    {
        $users = DB::table("users")
            ->where("tc", $request->tc)
            ->where("phone", $request->phone)
            ->first();

        $dietician = DB::table("dieticians")
            ->where("_id", $request->dietician_id)
            ->first();

        $dataInsert = [
            "ditican_id" => $dietician,
            "user_id" =>  $users,
            "morning"=> $request->morning,
            "noon" => $request->noon,
            "vesper" =>$request->vesper,
            "birding" => $request->birding, //Çaktırma kuşluk vakti bu :D
            "afternoon" => $request->afernoon,
            "night" => $request->night
        ];
        if ($dataInsert) {
            return response()->json(["data" => $dataInsert], 200);
        } else {
            return response()->json(["success" => false, "title" => "Başarısız!", "msg" => "Bu Bilgilere Ait Bir Kullanıcı Bulunmamaktadır."], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function update($id, Request $request)
    {
        if ($request->id) {
            $user = User::where("_id", $id)->first();
            if ($user) {
                $data = $request->except("_token");
                $update = User::where("_id", $id)
                    ->update($data);
                if ($update) {
                    return response()->json(["success" => true, "title" => "Başarılı!", "msg" => "Danışman Ayarları Başarıyla Güncellendi"], 200, [], JSON_UNESCAPED_UNICODE);
                } else {
                    return response()->json("Güncelleme İşlemi Yapılamadı", 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json("Böyle Bir Kullanıcı Bulunmamaktadır", 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function destroy($id)
    {
        if ($this->status != "admin") {
            return response()->json("Bu İşlem İçin Yetkili Değilsiniz", 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            User::where("_id", $id)
                ->delete();
            return response()->json("Silme İşlemi Başarılı", 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

}
