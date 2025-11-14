<?php
$routes->group('admin', ['namespace' => 'App\Controllers\Admin','filter' => 'group:user,admin'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
});