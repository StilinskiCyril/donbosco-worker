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

    // landing
    public function landingPage()
    {
        return view('landing');
    }
}
