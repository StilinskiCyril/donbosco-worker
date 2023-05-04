<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // manage projects page
    public function managePage()
    {
        return view('projects');
    }
}
