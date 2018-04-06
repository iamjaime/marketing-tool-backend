<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\OrderRepository as Order;
use App\Repositories\UserProvidingServiceRepository as UserProvidingService;
use App\Repositories\UserRepository as User;
use Illuminate\Support\Facades\Config;
use Jenssegers\Agent\Agent;

class OrderController extends Controller
{
    protected $order;
    protected $userProvidingService;
    protected $user;
    
    public function __construct(Order $order, UserProvidingService $userProvidingService, User $user){
        $this->order = $order;
        $this->userProvidingService = $userProvidingService;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function providerOrders($providerId)
    {
        $order = $this->order->findAllByProviderId($providerId, false);
        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personalProviderOrders($providerId, $providerAccountId)
    {
        $order = $this->order->findAllByProviderIdAndFillerId($providerId, $providerAccountId, $this->userId(), false);

        if(!$order){
            return response()->json([
                'success' => false,
                'data' => [
                    "account" => ['There is no record of the account provided.']
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ownedOrders($providerId)
    {
        $order = $this->order->findAllByProviderIdAndBuyerId($providerId, $this->userId(), false);
        return response()->json([
            'success' => true,
            'data' => $order
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
        $rules = $this->order->create_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        $credits = $this->order->getCreditsNeeded($data['quantity']);
        if(!$this->user->hasEnoughCredits($this->userId(), $credits)){
            return response()->json([
                'success' => false,
                'data' => [
                    "credits" => ['You do not have enough credits to make this order.']
                ]
            ], 400);
        }

        //If we pass validation lets create user and output success :)
        if($data['automatic']){
            $order = $this->order->createOneOrderAndAutomateTheRest($this->userId(), $data);
        }else{
            $order = $this->order->create($this->userId(), $data);
        }

        return response()->json([
            'success' => true,
            'data' => $order
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
        $order = $this->order->find($id);
        return response()->json([
            'success' => true,
            'data' => $order
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->order->update_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //If we pass validation lets update user and output success :)
        $order = $this->order->update($id, $data);

        return response()->json([
            'success' => true,
            'data' => $order
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
        $order = $this->order->delete($id);
        return response()->json([
            'success' => true
        ], 200);
    }

    /**
     * Handles Filling an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fill(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->userProvidingService->create_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //Validate if the order was filled within the last X amount of hours.....
        if($this->order->userAlreadyFilledThisOrder($this->userId(),$data['order_id'], $data['provider_id'], $data['provider_account_id'])){
            return response()->json([
                'success' => false,
                'data' => [
                    "job" => ['This order was already filled within the last ' . Config::get('marketingtool.job_limit_per_hour') . ' hours']
                ]
            ], 400);
        }

        //Validate if the order was filled within the last X amount of hours BUT still has fills remaining.....
        if($this->order->userAlreadyFilledThisOrderButHasFillsRemaining($this->userId(),$data['order_id'], $data['provider_id'], $data['provider_account_id'])){
            return response()->json([
                'success' => false,
                'data' => [
                    "job" => ['This order was already filled within the last ' . Config::get('marketingtool.job_fill_times_per_hour') . ' hours']
                ]
            ], 400);
        }


        //Validate Facebook Post......
        if($data['provider_id'] == 1){

            $fbPostValidation = $this->userProvidingService->validateFacebookPost($data, $this->userId());
            if(!$fbPostValidation){
                return response()->json([
                    'success' => false,
                    'data' => [
                        "job" => ['There is no record of the job being completed.']
                    ]
                ], 400);
            }
        }


        //If we pass validation lets fill the order
        $order = $this->userProvidingService->create($this->userId(), $data);

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pool()
    {
        $user = $this->user->find(Config::get('marketingtool.smi_pool_account_id'));

        $networth = Config::get('marketingtool.net_worth');
        $systemCommission = Config::get('marketingtool.system_commission');

        $oneCreditWorth = $networth + $systemCommission;

        $pool = $user->credits * $oneCreditWorth;

        return response()->json([
            'success' => true,
            'data' => number_format($pool, 2)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function orderStyle($id)
    {
        $order = $this->order->find($id);
        $agent = new Agent();

        $output['order'] = $order;
        $output['agent'] = $agent;

        //If we have instagram page share....
        if(strpos($order->target_url, 'https://www.instagram.com/') !== false){
            $boom = explode('/', $order->target_url);
            $instagramUsername = $boom[3];
            $output['instagram_username'] = $instagramUsername;
        }

        return view('facebook.smi_fb_share_order_style', $output);
    }


    /**
     * Display the specified resource
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function localOrders(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $distance = $request->get('distance');
        $per_page = $request->get('per_page');

        if(!$per_page){ $per_page = 10; }

        $isComplete = false; //is the order complete OR is the order still in progress?
        $order = $this->order->findNearby($lat, $lng, $distance, $isComplete)->paginate($per_page);
        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }
}
