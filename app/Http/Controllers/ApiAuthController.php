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
            $response = [
                'error'     => true,
                "message"   => "There are problems with your input",
                "messages"  => $validator->errors()->all()
            ];
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
            $response = [
                'error' => false,
                'message' => 'User created',
                'token'   => $token,
                'data' => $user
            ];
            return response()->json($response, 201);
        }

        $response = [
            'error' => true,
            'message' => 'Failed to save user'
        ];

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
            $response = [
                'error'     => true,
                "message"   => "There are problems with your input",
                "messages"  => $validator->errors()->all()
            ];
            return response()->json($response,400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $response = [
                    'error' => true,
                    'message'   => 'Invalid credentials'
                ];
                return response()->json($response, 401);
            }
        } catch (JWTException $e) {
            $response = [
                'error' => true,
                'message'   => 'Could not create token'
            ];
            return response()->json($response, 500);
        }

        $user = Auth::user();
        $response = [
            'error' => false,
            'token'   => $token,
            'data'  => $user
        ];
        return response()->json($response,200);
    }
}
