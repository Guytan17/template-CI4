<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends AdminController
{
    public function index()
    {
        return $this->render('admin/dashboard');
    }
}
