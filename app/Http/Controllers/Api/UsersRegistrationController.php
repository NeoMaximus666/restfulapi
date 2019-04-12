<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Http\Requests\UsersRegistrationValid;

class UsersRegistrationController extends Controller
{
    //реєстрація user через api
    public function store(UsersRegistrationValid $request)
    {

        /*$this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);*/


        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if ($user->save()) {
            $token = null;
            try {
                if (!$token = \auth('api')->attempt($credentials)) {
                    return response()->json(['msg' => 'Email or Password are incorrect'], 404);
                }
            } catch (JWTException $e) {
                return response()->json(['msg' => 'failed_to_create_token'], 400);
            }

            $user->signin = [
                'href' => 'api/auth/login',
                'method' => 'POST',
                'params' => 'email, password'
            ];

            $response = [
                'msg' => 'User created',
                'user' => $user,
                'token' => $token
            ];

            return response()->json($response, 201);
        }
    }
}
