<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
   public function index()
    {
        $setting = Setting::first();
        return  response()->json($setting, 200);
    }


    public function update(Request $request,string $id)
    {
       $setting = Setting::first();
        $setting->update($request->all());
        return  $setting;
    }


}
