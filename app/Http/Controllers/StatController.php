<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\StatRepository as Stat;

class StatController extends Controller
{

    protected $stats;

    public function __construct(Stat $stat)
    {
        $this->stats = $stat;
    }

    public function index()
    {
        $stats = $this->stats->getStats();
        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }
}
