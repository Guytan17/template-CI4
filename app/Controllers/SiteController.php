<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class SiteController extends BaseController
{
    protected $isAdmin = false;
}
