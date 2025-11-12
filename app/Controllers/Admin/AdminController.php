<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class AdminController extends BaseController
{
    public function render($view = null, $data = [], $is_admin = true)
    {
        return parent::render($view, $data, true);
    }
}
