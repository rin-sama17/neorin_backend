<?php
namespace App\Traits;

trait HttpResponses{


    protected function success ($data, $message=null, $code=200){
        return response()->json([
            "statusTxt"=>"request was successful",
            "status"=>true,
            "message"=>$message,
            "data"=>$data
        ],$code);
}
    protected function error ($data, $message=null, $code){
        return response()->json([
            "statusTxt"=>"request error",
            "status"=>false,
            "message"=>$message,
            "data"=>$data
        ],$code);
}
    }
