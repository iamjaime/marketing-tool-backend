<?php

namespace App\Console\Commands;

use App\Models\AutomaticJob as AutoJob;
use App\Models\Order as Order;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;

class AutoJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smi:generate-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates jobs from the automatic jobs table';

    protected $autoJob;

    protected $order;

    protected $user;

    /**
     * Create a new command instance.
     *
     * @return mixed
     */
    public function __construct(AutoJob $automaticJob, Order $order, User $user)
    {
        parent::__construct();
        $this->autoJob = $automaticJob;
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->generateJobs();
    }

    /**
     * Handles generating new jobs
     */
    protected function generateJobs()
    {
        $jobs = $this->autoJob->where('is_complete', false)->with(['subscription', 'order'])->get();
        foreach($jobs as $job) {

            $orderData = $job->subscription->toArray();
            $quantity = $job->order->quantity;

            $costInCredits = $this->getCreditsNeeded($quantity);

            $user = $this->user->find($job->order->user_id);
            $user->credits = ($user->credits - $costInCredits);
            $user->save();

            $order = new Order();
            $order->fill($job->order->toArray());
            $order->user_id = $job->order->user_id;
            $order->subscription_payment_id = $job->subscription_payment_id;
            $order->service_provider_id = $job->order->service_provider_id;
            $order->service_id = $job->order->service_id;
            $order->total_cost = $costInCredits;
            $order->save();

            $job->days_remaining = $job->days_remaining - 1;

            if($job->days_remaining < 1){
                $jobData['is_complete'] = true;
                $jobData['days_remaining'] = $job->days_remaining;
            }else{
                $jobData['days_remaining'] = $job->days_remaining;
            }

            $this->updateAutoJob($job->id, $jobData);
        }
    }

    /**
     * Handles updating auto job record
     *
     * @param int   $id
     * @param array $data
     * @return AutomaticJob
     */
    protected function updateAutoJob($id, array $data)
    {
        $job = $this->autoJob->where('id', $id)->first();

        if(!$job){
            return false;
        }

        $job->fill($data);
        $job->save();

        return $job;
    }

    /**
     * Handles getting the credits needed
     *
     * @param $quantity
     * @return mixed
     */
    protected function getCreditsNeeded($quantity)
    {
        $costInDollars = $quantity * (Config::get('marketingtool.net_worth') + Config::get('marketingtool.system_commission'));
        $costInCredits = $costInDollars * 100;

        return $costInCredits;
    }
}
