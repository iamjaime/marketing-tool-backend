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
use App\Repositories\AutomaticJobRepository;

class OrderRepository implements OrderRepositoryContract
{

    protected $order;
    protected $serviceProvider;
    protected $user;
    protected $userAttachedServiceProvider;
    protected $userProvidingService;
    protected $autoJob;

    /**
     * Handles the create new order validation rules.
     * @var array
     */
    public $create_rules = [
        'service_provider_id' => 'required|exists:service_providers,id',
        'service_id' => 'required|exists:services,id',
        'quantity' => 'required',
        'automatic' => 'required|boolean',
        'subscription_id' => 'required_if:automatic,true',
        'subscription_payment_id' => 'required_if:automatic,true',
        'subscription_begin_date' => 'required_if:automatic,true',
        'subscription_end_date' => 'required_if:automatic,true'
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

    public $local_rules = [
        'latitude' => 'required',
        'longitude' => 'required',
        'distance' => 'required',
//        'units' => 'required', //km or miles
    ];


    public function __construct(Order $order, ServiceProvider $serviceProvider, User $user, UserAttachedServiceProvider $userAttachedServiceProviderRepository, UserProvidingService $userProvidingService, AutomaticJobRepository $automaticJobRepository){
        $this->order = $order;
        $this->serviceProvider = $serviceProvider;
        $this->user = $user;
        $this->userAttachedServiceProvider = $userAttachedServiceProviderRepository;
        $this->userProvidingService = $userProvidingService;
        $this->autoJob = $automaticJobRepository;
    }

    /**
     * Handles Finding a order by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $order = $this->order->where('id', $id)->with('autoJob')->first();
        return $order;
    }

    /**
     * Handles finding the nearby orders
     *
     * @param $lat
     * @param $lng
     * @param $distance
     * @param $isComplete
     * @return mixed
     */
    public function findNearby($lat, $lng, $distance = 5, $isComplete = false)
    {
        return $this->order->where('is_complete', $isComplete)->where('is_a_local_job', true)->isWithinMaxDistance($lat, $lng, $distance);
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
            ->where('fills_remaining', '=', 0)
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
     * Handles getting the orders that have already been filled but have remaining fills left in order to complete the job
     * by a specific user.
     *
     * @param $hoursOffset          How many hours prior was the order filled?
     * @param $userId               The user who filled the order
     * @param $providerId           The provider id ( facebook, twitter, instagram etc. )
     * @param $providerAccountId    The provider account id.
     * @return mixed
     */
    protected function getFilledOrdersWithFillsRemaining($hoursOffset, $userId, $providerId, $providerAccountId)
    {
        $accountProvidingService = $this->userAttachedServiceProvider->findByUserIdAndProviderId($userId, $providerId, $providerAccountId);
        if(!$accountProvidingService){
            return false;
        }
        $userProvidingServiceId = $accountProvidingService->id;

        $ordersArray = [];

        //SELECT * FROM user_providing_services as ups where created_at < timestampadd(hour, -12, now());
        $ordersAlreadyFilled = $this->userProvidingService
            ->where('updated_at', '>', Carbon::now()->subHours($hoursOffset))
            ->where('providing_service_id', '=', $userProvidingServiceId)
            ->where('fills_remaining', '>', 0)
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
     * Handles Creating the initial subscription order and automating the rest
     *
     * @param $user_id
     * @param array $data
     * @return Order
     */
    public function createOneOrderAndAutomateTheRest($user_id, array $data)
    {
        //first we need to get the number of days between the beginning of the subscription
        //and the ending of the subscription
        $days = $this->getDaysBetweenSubscription($data['subscription_begin_date'], $data['subscription_end_date']);
        //then we need to divide the total order quantity by the number above.....
        $dailyViews = $data['quantity'] / floor($days);

        $costInCredits = $this->getCreditsNeeded($dailyViews);

        //now lets deduct the total cost from the purchaser's account....
        $this->user->deductCredits($user_id, $costInCredits);

        $this->order = new Order();
        $this->order->fill($data);
        $this->order->quantity = $dailyViews;
        $this->order->user_id = $user_id;
        $this->order->subscription_payment_id = $data['subscription_payment_id'];
        $this->order->service_provider_id = $data['service_provider_id'];
        $this->order->service_id = $data['service_id'];
        $this->order->total_cost = $costInCredits;
        $this->order->save();


        //format the subscription dates....
        $data['subscription_begin_date'] = Carbon::createFromFormat("U", $data['subscription_begin_date'])->format("Y-m-d H:i:s");
        $data['subscription_end_date'] = Carbon::createFromFormat("U", $data['subscription_end_date'])->format("Y-m-d H:i:s");

        $autoJobData = [
            'order_id' => $this->order->id,
            'subscription_payment_id' => $data['subscription_payment_id'],
            'days_remaining' => $days - 1, //deduct the order we already created for today.
            'begin_date' => $data['subscription_begin_date'],
            'end_date' => $data['subscription_end_date']
        ];

        $this->autoJob->create($autoJobData);

        return $this->order;
    }


    /**
     * Handles getting the amount of days from beginning to end of subscription
     *
     * @param $beginDate
     * @param $endDate
     * @return int
     */
    protected function getDaysBetweenSubscription($beginDate, $endDate)
    {
        $theBeginning = Carbon::createFromTimestamp($beginDate);
        $theEnd = Carbon::createFromTimestamp($endDate);

        return $theBeginning->diffInDays($theEnd);
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


    /**
     * Handles checking if user already filled an order within the last X amount of hours
     *
     * @param $fillerId
     * @param $orderId
     * @param $providerId
     * @param $providerAccountId
     * @return bool
     */
    public function userAlreadyFilledThisOrder($fillerId, $orderId, $providerId, $providerAccountId)
    {
        $orders_that_were_filled = $this->getFilledOrders(Config::get('marketingtool.job_limit_per_hour'), $fillerId, $providerId, $providerAccountId);

        foreach($orders_that_were_filled as $orderFilled) {
            if($orderFilled == $orderId){
                return true;
            }
        }

        return false;
    }


    /**
     * Handles checking if user already filled an order within X amount of time but still has fills remaining
     *
     * @param $fillerId
     * @param $orderId
     * @param $providerId
     * @param $providerAccountId
     * @return bool
     */
    public function userAlreadyFilledThisOrderButHasFillsRemaining($fillerId, $orderId, $providerId, $providerAccountId)
    {
        $orders_with_fills_remaining = $this->getFilledOrdersWithFillsRemaining(Config::get('marketingtool.job_fill_times_per_hour'), $fillerId, $providerId, $providerAccountId);

        foreach($orders_with_fills_remaining as $orderFilled) {
            if($orderFilled == $orderId){
                return true;
            }
        }

        return false;
    }

}
