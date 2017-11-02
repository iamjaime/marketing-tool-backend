<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepository as OrderRepositoryContract;
use App\Models\Order;

class OrderRepository implements OrderRepositoryContract
{

    protected $order;

    /**
     * Handles the create new order validation rules.
     * @var array
     */
    public $create_rules = [
        'name' => 'required'
    ];

    /**
     * Handles the update order validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required'
    ];


    public function __construct(Order $order){
        $this->order = $order;
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
     * Handles creating new order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data)
    {
        $this->order = new Order();
        $this->order->fill($data);
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
