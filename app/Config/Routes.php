<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//Route Shield
require APPPATH . 'Config/Routes/Shield.php';

//Route de l'administration
require APPPATH . 'Config/Routes/Admin.php';

//Route du site (front)
require APPPATH . 'Config/Routes/Site.php';

//Route pour l'API
require APPPATH . 'Config/Routes/Api.php';