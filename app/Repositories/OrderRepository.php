<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepository as OrderRepositoryContract;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use App\Models\ServiceProvider;

class OrderRepository implements OrderRepositoryContract
{

    protected $order;
    protected $serviceProvider;

    /**
     * Handles the create new order validation rules.
     * @var array
     */
    public $create_rules = [
        'service_provider_id' => 'required|exists:service_providers,id',
        'service_id' => 'required|exists:services,id',
        'quantity' => 'required'
    ];

    /**
     * Handles the update order validation rules.
     * @var array
     */
    public $update_rules = [
        'service_provider_id' => 'sometimes|required|exists:service_providers,id',
        'service_id' => 'sometimes|required|exists:services,id',
        'quantity' => 'sometimes|required',
        'is_complete' => 'sometimes|required'
    ];


    public function __construct(Order $order, ServiceProvider $serviceProvider){
        $this->order = $order;
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * Handles Finding a order by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $order = $this->order->where('id', $id)->first();
        return $order;
    }

    /**
     * Handles Finding all orders from a specific service provider
     *
     * @param Integer $providerId
     * @param Boolean $isCompleted
     * @return mixed
     */
    public function findAllByProviderId($providerId, $isCompleted=false)
    {
        $orders = $this->serviceProvider->where('id', '=', $providerId)
            ->with(['orders' => function($q) use ($isCompleted){
            $q->where('is_complete', '=', $isCompleted);
        },
                'orders.usersProvidingService',
                'orders.service',
                'orders.buyer'
            ])->get();

        return $orders;
    }


    /**
     * Handles creating new order
     *
     * @param integer $user_id
     * @param array $data
     * @return Order
     */
    public function create($user_id, array $data)
    {
        $this->order = new Order();
        $this->order->fill($data);
        $this->order->user_id = $user_id;
        $this->order->service_provider_id = $data['service_provider_id'];
        $this->order->service_id = $data['service_id'];

        $costInDollars = $data['quantity'] * (Config::get('marketingtool.net_worth') + Config::get('marketingtool.system_commission'));
        $costInCredits = $costInDollars * 100;

        $this->order->total_cost = $costInCredits;

        $this->order->save();

        return $this->order;
    }

    /**
     * Handles updating order
     *
     * @param array $data
     * @return Order
     */
    public function update($id, array $data)
    {
        $order = $this->order->where('id', $id)->first();
        if(!$order){
            return false;
        }
        $order->fill($data);
        $order->save();

        return $order;
    }

    /**
     * Handles Deleting order
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $order = $this->order->where('id', $id)->first();
        if(!$order){
            return false;
        }
        $order->delete();
        return true;
    }

}
