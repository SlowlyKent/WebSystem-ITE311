<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index()
    {
        return view('index'); // homepage
    }

    public function about()
    {
        return view('about'); // about page
    }

    public function contact()
    {
        return view('contact'); // contact page
    }
}