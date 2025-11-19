<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends AdminController
{
    protected $title = 'Dashboard';
    protected $menu = 'dashboard';
    protected $breadcrumb = [['text' => 'Dashboard']];
    public function index()
    {
        return $this->render('admin/dashboard');
    }
}
