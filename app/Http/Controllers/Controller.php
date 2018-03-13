<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use App\Utils\ValidatesRequestsWithWrapper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

use

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequestsWithWrapper;

    /**
     * Gets the logged in user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return Auth::user();
    }

    /**
     * Gets the logged in user's id
     *
     * @return int|null
     */
    public function userId()
    {
        return Auth::id();
    }
}
