<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ForgotRequest;
use Illuminate\Support\Facades\Mail;

class ForgotController extends Controller
{
    //
    public function forgot(ForgotRequest $request)
    {
        $email = $request->input('email');

        if (User::where('email', $email)->doesntExist()){
            return response([
                'message' => 'User doen\'t exist!'
            ], 404);
        }

        $token = Str::random(10);

        try {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token
            ]);

            //send email
            Mail::send('Mails.forgot', ['token' => $token], function (Message $message) use ($email) {
                $message->to($email);
                $message->subject('Reset your password');
            });

            return response([
                'message' => 'Check your email!'
            ]);

        }catch (\Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }

    }

    public function  reset(ResetRequest $request){
        $token = $request->input('token');

        if(!$passwordResets = DB::table('password_resets')->where('token', $token)->first()){
            return response([
                'message' => 'Invalide token!'
            ], 400);
        }

        /** @var User $user */
        if (!$user = User::where('email', $passwordResets->email)->first()){
            return response([
                'message' => 'User doesn\'t exist'
            ], 404);
        }

        $user->password= Hash::make($request->input('password'));
        $user->save();

        return response([
            'message'=> 'success'
        ]);
    }
}
