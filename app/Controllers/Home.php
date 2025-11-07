<?php

namespace App\Controllers;

class Home extends SiteController
{
    public function index(): string
    {
        return view('welcome_message');
    }
}
