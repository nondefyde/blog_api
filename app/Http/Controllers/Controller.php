<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function setTrim(Request $request){
        $request->merge(array_map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            } else {
                return $value;
            }
        }, $request->all()));
    }

    //for response
    public function createResponse($error, $data = null, $message = null,$messages = array()){
        $response = array();
        $response['error'] = $error;
        if($message != null){
            $response['message'] = $message;
        }
        if(!empty($messages)){
            $response['messages'] = $messages;
        }
        if($data != null){
            $response['data'] = $data;
        }

        return $response;
    }

    public function createAuthResponse($error, $token, $data = null, $message = null){
        $response = array();
        $response['error'] = $error;
        $response['token'] = $token;
        if($message != null){
            $response['message'] = $message;
        }
        if($data != null){
            $response['data'] = $data;
        }

        return $response;
    }
}
