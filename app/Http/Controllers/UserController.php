<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository as User;
use App\Repositories\UserAttachedServiceProviderRepository as UserAttachedServiceProvider;

//forgot password
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


//Mailer....
use Illuminate\Support\Facades\Mail;
use App\Mail\UserSignup;



class UserController extends Controller
{


    protected $user;
    protected $userAttachedServiceProvider;


    public function __construct(User $user, UserAttachedServiceProvider $userAttachedServiceProvider){
        $this->user = $user;
        $this->userAttachedServiceProvider = $userAttachedServiceProvider;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->user->find($this->userId());
        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->user->create_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }


        //If we pass validation lets create user and output success :)
        $user = $this->user->create($data);
        Mail::to($user->email)->send(new UserSignup($user));

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->user->find($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->user->update_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //If we pass validation lets update user and output success :)
        $user = $this->user->update($this->userId(), $data);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->user->delete($id);
        return response()->json([
            'success' => true
        ], 200);
    }


    /**
     * Handles attaching a user's account to a social media network provider.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachUserToServiceProvider(Request $request)
    {
        $data = $request->get('data');

        $attachedUser = $this->userAttachedServiceProvider->findByUserIdAndProviderId($this->userId(), $data['provider_id'], $data['provider_account_id']);

        if(!$attachedUser){
            //validate....
            $rules = $this->userAttachedServiceProvider->create_rules;
            $validator = $this->validate($request, $rules);

            if(!empty($validator)){
                return response()->json([
                    'success' => false,
                    'data' => $validator
                ], 400);
            }


            //If we pass validation lets create and output success :)
            $attachUserToServiceProvider = $this->userAttachedServiceProvider->create($this->userId(), $data);

            return response()->json([
                'success' => true,
                'data' => $attachUserToServiceProvider
            ], 201);

        }

        return response()->json([
            'success' => true,
            'data' => []
        ], 200);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscriptions()
    {
        $user = $this->user->subscriptions($this->userId());
        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStripeWithdrawals()
    {
        $perPage = 10;

        $user = $this->user->getStripeWithdrawals($this->userId())->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }
      

}
