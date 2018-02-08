<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getResetToken(Request $request)
    {
        $data = $request->input('data');

        $this->validate($request, ['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();

        if(empty($user)){
            return response()->json([
                'success' => false,
                'data' => ['user' => ['The user does not exist']]
            ], 400);
        }

            $rand = mt_rand(9999999999, 99999999999);
            $token = Hash::make($rand);


            $resetData = DB::table('password_resets')->where('email', $user->email)->first();

            if(!empty($resetData)){
                DB::table('password_resets')->update(['email' => $user->email, 'token' => $token]);
            }else{
                DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token]);
            }

        return response()->json([
            'success' => true,
            'token'  => $token
        ], 200);
    }
}
