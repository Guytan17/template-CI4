<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class SiteController extends BaseController
{
    public function render($view = null, $data = [], $is_admin = false)
    {
        return parent::render($view, $data, false);
    }
}
