<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserProvidingServiceRepository as UserProvidingServiceRepositoryContract;
use App\Models\UserProvidingService;
use App\Models\Order;
use App\Repositories\UserAttachedServiceProviderRepository as UserAttachedService;
use App\Repositories\UserRepository as User;
use Illuminate\Support\Facades\Config;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as Facebook;

class UserProvidingServiceRepository implements UserProvidingServiceRepositoryContract
{

    protected $userProvidingService;
    protected $order;
    protected $userAttachedService;
    protected $user;
    protected $facebook;


    /**
     * Handles the create new user providing service validation rules.
     * @var array
     */
    public $create_rules = [
        'order_id' => 'required|exists:orders,id',
        'provider_id' => 'required|exists:service_providers,id',
        'provider_account_id' => 'required|exists:user_attached_service_providers,provider_account_id'
    ];

    /**
     * Handles the update user providing service validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required'
    ];


    public function __construct(UserProvidingService $userProvidingService, Order $order, UserAttachedService $userAttachedService, User $user, Facebook $facebook){
        $this->userProvidingService = $userProvidingService;
        $this->order = $order;
        $this->userAttachedService = $userAttachedService;
        $this->user = $user;
        $this->facebook = $facebook;
    }

    /**
     * Handles Finding a user providing service by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        return $userProvidingService;
    }


    /**
     * Handles creating new user providing service
     *
     * @param integer $user_id  The authenticated user's id.
     * @param array $data
     * @return mixed
     */
    public function create($user_id, array $data)
    {
        $socialAccount = $this->userAttachedService->findByUserIdAndProviderId($user_id, $data['provider_id'], $data['provider_account_id']);

        if(!$socialAccount){
            return false;
        }

        $order = $this->order->where('id', '=', $data['order_id'])->first();

        //get the amount of views that we need.
        $remainingToFill = $this->getTrafficBalance($order);

        //get the amount of traffic that we can fill with this social account.
        $myTraffic = $socialAccount->traffic;

        if($myTraffic >= $remainingToFill){
            //we have enough traffic to fill the order....
            $creditsInDollars = $remainingToFill * Config::get('marketingtool.net_worth');
            $userCredits = $creditsInDollars * 100;


            $systemCreditsInDollars = $remainingToFill * Config::get('marketingtool.system_commission');
            $systemCredits = $systemCreditsInDollars * 100;


            //Lets get the pool credits from the system credits.....
            $poolCredits = $systemCredits * Config::get('marketingtool.smi_pool');


            //deduct from system credits and append to pool credits
            $systemCredits = $systemCredits - $poolCredits;

            //now we can send ourselves the system commission
            $this->user->addCredits(Config::get('marketingtool.admin_account_id'), $systemCredits);
            $this->user->addCredits(Config::get('marketingtool.smi_pool_account_id'), $poolCredits);
            $this->user->addCredits($user_id, $userCredits);


            $this->userProvidingService = new UserProvidingService();
            $this->userProvidingService->order_id = $order->id;
            $this->userProvidingService->providing_service_id = $socialAccount->id;
            $this->userProvidingService->buying_service_user_id = $order->user_id;
            $this->userProvidingService->traffic_provided = $socialAccount->traffic;
            $this->userProvidingService->credits_paid = $userCredits;
            $this->userProvidingService->save();

            //now we need to update the progress of the order....
            $progress = $order->progress + $remainingToFill;
            $order->progress = $progress;
            $order->is_complete = true;
            $order->save();


            return $this->userProvidingService;
        }else{
            //my traffic isn't enough to fill the order....
            $creditsInDollars = $myTraffic * Config::get('marketingtool.net_worth');
            $userCredits = $creditsInDollars * 100;

            $systemCreditsInDollars = $myTraffic * Config::get('marketingtool.system_commission');
            $systemCredits = $systemCreditsInDollars * 100;

            //Lets get the pool credits from the system credits.....
            $poolCredits = $systemCredits * Config::get('marketingtool.smi_pool');

            //deduct from system credits and append to pool credits
            $systemCredits = $systemCredits - $poolCredits;


            //now we can send ourselves the system commission
            $this->user->addCredits(Config::get('marketingtool.admin_account_id'), $systemCredits);
            $this->user->addCredits(Config::get('marketingtool.smi_pool_account_id'), $poolCredits);
            $this->user->addCredits($user_id, $userCredits);

            $this->userProvidingService = new UserProvidingService();
            $this->userProvidingService->order_id = $order->id;
            $this->userProvidingService->providing_service_id = $socialAccount->id;
            $this->userProvidingService->buying_service_user_id = $order->user_id;
            $this->userProvidingService->traffic_provided = $socialAccount->traffic;
            $this->userProvidingService->credits_paid = $userCredits;
            $this->userProvidingService->save();

            //now we need to update the progress of the order....
            $progress = $order->progress + $myTraffic;
            $order->progress = $progress;

            if($progress >= $order->quantity){
                $order->is_complete = true;
            }

            $order->save();

            return $this->userProvidingService;
        }

        return false;
    }


    /**
     * Handles getting the amount remaining to fill the order.
     *
     * @param $order
     * @return mixed
     */
    private function getTrafficBalance($order)
    {
        $balance = $order->quantity - $order->progress;
        return $balance;
    }


    /**
     * Handles updating user providing service
     *
     * @param array $data
     * @return UserProvidingService
     */
    public function update($id, array $data)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        if(!$userProvidingService){
            return false;
        }
        $userProvidingService->fill($data);
        $userProvidingService->save();

        return $userProvidingService;
    }

    /**
     * Handles Deleting user providing service
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        if(!$userProvidingService){
            return false;
        }
        $userProvidingService->delete();
        return true;
    }


    /**
     * Handles validating facebook post from user
     *
     * @param $data
     * @return bool
     */
    public function validateFacebookPost($data, $user_id)
    {

        $order = $this->order->where('id', '=', $data['order_id'])->first();

        try {
            $response = $this->facebook->get('/'. $data['provider_account_id'].'?fields=posts.limit(1){caption,link,privacy}', $data['fb_token']);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }

        $userNode = $response->getGraphUser();

        $url = $userNode['posts'][0]['link'];
        $originalUrl = $url;

        $link = parse_url($url, PHP_URL_QUERY);

        if($link){
            $refParam = '&smiref=' . $user_id;
        }else{
            $refParam = '?smiref=' . $user_id;
        }

        $privacy = $userNode['posts'][0]['privacy']['description'];

        //remove trailing slash if it has one
        $link = rtrim($url, '/');

        return [
            'original_url' => $originalUrl,
            'link' => $link,
            'ref_param' => $refParam,
            'order_url_and_ref' => $order->url . $refParam
        ];

        if($order->url . $refParam == $link && $privacy == 'Public' || $order->url == $link && $privacy == 'Your friends'){
            return true;
        }else{
            return false;
        }
    }

}
