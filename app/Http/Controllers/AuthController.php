<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {

            if (Auth::attempt($request->only( 'email', 'password'))){
                /** @var User $user */
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;

                return response([
                    'message'=> 'success',
                    'token' => $token,
                    'user' => $user
                ]);
            }
        }catch (\Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }

    }

    public function user()
    {
        return Auth::user();
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'first_name'=> $request->input('first_name'),
                'last_name'=> $request->input('last_name'),
                'email'=> $request->input('email'),
                'password'=> Hash::make($request->input('password')),
            ]);

            return $user;
        }catch (\Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }


}
