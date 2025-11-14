<?php

namespace App\Controllers;

class Home extends SiteController
{
    public function index(): string
    {
        return $this->render('/front/home');
    }
}
