<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepository as OrderRepositoryContract;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use App\Models\ServiceProvider;
use App\Repositories\UserRepository as User;
use App\Repositories\UserAttachedServiceProviderRepository as UserAttachedServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\UserProvidingService;

class OrderRepository implements OrderRepositoryContract
{

    protected $order;
    protected $serviceProvider;
    protected $user;
    protected $userAttachedServiceProvider;
    protected $userProvidingService;

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


    public function __construct(Order $order, ServiceProvider $serviceProvider, User $user, UserAttachedServiceProvider $userAttachedServiceProviderRepository, UserProvidingService $userProvidingService){
        $this->order = $order;
        $this->serviceProvider = $serviceProvider;
        $this->user = $user;
        $this->userAttachedServiceProvider = $userAttachedServiceProviderRepository;
        $this->userProvidingService = $userProvidingService;
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
     * Handles Finding all orders from a specific service provider
     * That belong to a specific user
     *
     * @param Integer $providerId
     * @param Integer $buyerId
     * @param Boolean $isCompleted
     * @return mixed
     */
    public function findAllByProviderIdAndBuyerId($providerId, $buyerId, $isCompleted=false)
    {
        $orders = $this->serviceProvider->where('id', '=', $providerId)
            ->with(['orders' => function($q) use ($isCompleted, $buyerId){
            $q->where('is_complete', '=', $isCompleted)->where('user_id', '=', $buyerId);
        },
                'orders.usersProvidingService',
                'orders.service',
                'orders.buyer'
            ])->get();

        return $orders;
    }

    /**
     * Handles Finding all orders from a specific service provider
     * That have been filled by a specific user
     *
     * @param Integer $providerId
     * @param Integer $providerAccountId
     * @param Integer $fillerId
     * @param Boolean $isCompleted
     * @return mixed
     */
    public function findAllByProviderIdAndFillerId($providerId, $providerAccountId, $fillerId, $isCompleted=false)
    {
        $orders_that_were_filled = $this->getFilledOrders(Config::get('marketingtool.job_limit_per_hour'), $fillerId, $providerId, $providerAccountId);

        if($orders_that_were_filled){
            $orders = $this->order->where('is_complete', '=', $isCompleted)->whereNotIn('id', $orders_that_were_filled)->get();
        }else{
            $orders = $this->order->where('is_complete', '=', $isCompleted)->get();
        }

        return $orders;
    }


    /**
     * Handles getting the orders that have already been filled by a specific user.
     *
     * @param $hoursOffset          How many hours prior was the order filled?
     * @param $userId               The user who filled the order
     * @param $providerId           The provider id ( facebook, twitter, instagram etc. )
     * @param $providerAccountId    The provider account id.
     * @return mixed
     */
    protected function getFilledOrders($hoursOffset, $userId, $providerId, $providerAccountId)
    {
        $accountProvidingService = $this->userAttachedServiceProvider->findByUserIdAndProviderId($userId, $providerId, $providerAccountId);
        if(!$accountProvidingService){
            return false;
        }
        $userProvidingServiceId = $accountProvidingService->id;

        $ordersArray = [];

        //SELECT * FROM user_providing_services as ups where created_at < timestampadd(hour, -12, now());
        $ordersAlreadyFilled = $this->userProvidingService
            ->where('created_at', '>', Carbon::now()->subHours($hoursOffset))
            ->where('providing_service_id', '=', $userProvidingServiceId)
            ->select('user_providing_services.order_id')
            ->get();

        foreach($ordersAlreadyFilled as $order) {
            if(!in_array($order->order_id, $ordersArray)){
                array_push($ordersArray, $order->order_id);
            }
        }

        return $ordersArray;
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
        $costInCredits = $this->getCreditsNeeded($data['quantity']);

        //now lets deduct the total cost from the purchaser's account....
        $this->user->deductCredits($user_id, $costInCredits);

        $this->order = new Order();
        $this->order->fill($data);
        $this->order->user_id = $user_id;
        $this->order->service_provider_id = $data['service_provider_id'];
        $this->order->service_id = $data['service_id'];

        $this->order->total_cost = $costInCredits;

        $this->order->save();

        return $this->order;
    }

    /**
     * Handles getting the credits needed
     *
     * @param $quantity
     * @return mixed
     */
    public function getCreditsNeeded($quantity)
    {
        $costInDollars = $quantity * (Config::get('marketingtool.net_worth') + Config::get('marketingtool.system_commission'));
        $costInCredits = $costInDollars * 100;

        return $costInCredits;
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
