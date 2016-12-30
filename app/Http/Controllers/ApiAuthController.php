<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Auth;
use Validator;

class ApiAuthController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            $response = $this->createResponse(true,null,"There are problems with your input",$validator->errors()->all());
            return response()->json($response,400);
        }

        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => bcrypt($password),
            'user_type' => 2
        ]);

        if ($user->save()) {
            $token = JWTAuth::fromUser($user);
            $user->sigin_link = [
                'href' => 'api/v1/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            $response = $this->createAuthResponse(false,$token,$user,'User created');
            return response()->json($response, 201);
        }
        $response = $this->createResponse(true,null,'Failed to save user',null);
        return response()->json($response, 404);
    }

    /*
     *
     */
    public function signin(Request $request)
    {
        $this->setTrim($request);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $response = $this->createResponse(true,null,"There are problems with your input",$validator->errors()->all());
            return response()->json($response,400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $response = $this->createResponse(true,null,'Invalid credentials',null);
                return response()->json($response, 401);
            }
        } catch (JWTException $e) {
            $response = $this->createResponse(true,null,'Could not create token',null);
            return response()->json($response, 500);
        }

        $user = Auth::user();
        $response = $this->createAuthResponse(false,$token,$user,'Sign in successful');
        return response()->json($response,200);
    }
}
