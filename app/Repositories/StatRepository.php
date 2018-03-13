<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StatRepository
{

    /**
     * Handles returning the system stats.
     *
     * @return array
     */
    public function getStats()
    {
        $stats = [
            'potential_traffic' => $this->getTotalTraffic(),
            'total_workers' => $this->getTotalWorkers(),
            'total_completed_orders' => $this->getTotalCompletedOrders(),
            'total_views_provided' => $this->getTotalViewsProvided(),
            'families_helped' => $this->getFamiliesHelped()
        ];

        return $stats;
    }

    /**
     * Handles getting the total amount of traffic
     * in the SMI system. ( This means every users friends list count )
     */
    public function getTotalTraffic()
    {
        $traffic = DB::table('user_attached_service_providers')->sum('traffic');
        return $traffic;
    }


    /**
     * Handles getting the total workers count
     *
     * @return mixed
     */
    public function getTotalWorkers()
    {
        $workers = DB::table('users')->count();
        return $workers;
    }

    /**
     * Handles getting the total count of orders completed.
     *
     * @return mixed
     */
    public function getTotalCompletedOrders()
    {
        $orders = DB::table('orders')->where('is_complete', true)->count();
        return $orders;
    }

    /**
     * Handles getting the total views provided over all
     */
    public function getTotalViewsProvided()
    {
        $views = DB::table('orders')->sum('progress');
        return $views;
    }


    /**
     * Handles getting the families helped count
     *
     * @return mixed
     */
    public function getFamiliesHelped()
    {
        $families = DB::table('user_providing_services')->distinct('providing_service_id')->count('providing_service_id');
        return $families;
    }

}
