<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MofidController extends Controller
{
    public function loginToMofid(Request $request)
    {
        $data = [
            "grant_type" => "authorization_code",
            "redirect_uri" => "https://d.orbis.easytrader.ir/auth-callback",
            "code" => "Gkaa4mdwxnT-dbze9IkAA1Azy4W1J-ZSf1D08j22osg",
            "code_verifier" => "5fafdb1efd0647058ea87be3e2d807db79a5a5fadeb54416899c8f60385f11279f54148794bd4dc2be59ebadaa2f283d",
            "client_id" => "easy_pkce",
        ];
        $url = "https://account.emofid.com/connect/token";
        $response = Http::post($url, $data);

        //save mofid token in settings table
        $data = [
            "key" => "mofid.token",
            "value" => $response->object()->access_token,
        ];
        Settings::query()
            ->updateOrCreate(["key" => $data["key"]], $data);
    }
}
