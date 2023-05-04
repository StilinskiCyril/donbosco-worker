<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
   // dashboard page
    public function dashboardPage()
    {
        return view('dashboard');
    }

    // load stats
    public function loadStats(Request $request)
    {
        // TODO return results based on roles
        return response()->json([
            'donations_today' => 20000,
            'donations_this_month' => 268040,
            'active_projects' => 2,
            'total_donations' => 14050800,
            'donations_summary' => [76, 85, 101, 98, 87, 105, 91, 114, 94, 45, 87, 36],
            'labels' => ['Total Donations', 'Balance', 'Administration Fees'],
            'series' => [10000, 3000, 5000],
        ]);
    }

    // landing
    public function landingPage()
    {
        return view('landing');
    }
}
