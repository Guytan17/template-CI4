<?php

// Routes pour les comptes utilisateurs
$routes->group('account', ['namespace' => 'App\Controllers\Front'], function($routes) {
    // Inscription
    $routes->get('register', 'Account::register');
    $routes->post('register', 'Account::doRegister');

    // Profil (nécessite d'être connecté)
    $routes->get('profile', 'Account::profile', ['filter' => 'session']);
    $routes->post('profile', 'Account::updateProfile', ['filter' => 'session']);
    $routes->post('delete-avatar', 'Account::deleteAvatar', ['filter' => 'session']);
});

//dataTable
$routes->post('/datatable/searchdatatable', 'DataTable::searchdatatable');