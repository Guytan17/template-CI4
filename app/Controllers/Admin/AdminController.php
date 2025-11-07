<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class AdminController extends BaseController
{
    public function render($view, $data = [])
    {
        return parent::render($view, $data, true);
    }
}