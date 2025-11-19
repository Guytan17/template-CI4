<?php
$routes->group('admin', ['namespace' => 'App\Controllers\Admin','filter' => 'group:user,admin'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Routes pour la gestion des utilisateurs (admin uniquement)
    $routes->group('users', ['filter' => 'group:admin'], function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('form', 'Users::form');              // Création
        $routes->get('form/(:num)', 'Users::form/$1');    // Édition
        $routes->post('save', 'Users::save');             // Sauvegarde création
        $routes->post('save/(:num)', 'Users::save/$1');   // Sauvegarde mise à jour
        $routes->post('toggle-active/(:num)', 'Users::toggleActive/$1');
        $routes->get('delete/(:num)', 'Users::delete/$1');
        $routes->post('delete-avatar/(:num)', 'Users::deleteAvatar/$1');
    });
});