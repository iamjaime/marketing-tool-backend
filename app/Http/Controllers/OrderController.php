<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\OrderRepository as Order;
use App\Repositories\UserProvidingServiceRepository as UserProvidingService;
use App\Repositories\UserRepository as User;

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
        $order = $this->order->create($this->userId(), $data);

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

        //If we pass validation lets fill the order
        $order = $this->userProvidingService->create($this->userId(), $data);

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }
}
