<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class AdminController extends BaseController
{
    protected $isAdmin = true;
}
