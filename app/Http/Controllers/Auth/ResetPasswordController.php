<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }


    protected function guard()
    {
        return Auth::guard('api');
    }


    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $data = $request->input('data');
        return $data;
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required|exists:password_resets,token',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $data = $this->credentials($request);

        //validate....
        $rules = $this->rules();
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //$this->validate($request, $this->rules(), $this->validationErrorMessages());

        //first lets check to make sure that we have the correct user....
        $user = DB::table('password_resets')->where('email', $data['email'])->where('token', '=', $data['token'])->first();

        if($user) {
            $usr = User::where('email', $data['email'])->first();
            $usr->password = Hash::make($data['password']);
            $usr->save();

            $resetData = DB::table('password_resets')->where('email', $usr->email)->where('token', '=', $data['token'])->delete();
        }else{

            return response()->json([
                'success' => false,
                'data' => [
                    "account" => ['There is no record of this account trying to reset it\'s password']
                ]
            ], 400);

        }

        return response()->json([
            'success' => true
        ], 200);

    }

}
