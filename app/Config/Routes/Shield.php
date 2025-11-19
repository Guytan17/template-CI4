<?php
// Routes Shield personnalisées (utilisant nos controllers Front)
$routes->group('', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    // Login
    $routes->get('login', 'LoginController::loginView', ['as' => 'login']);
    $routes->post('login', 'LoginController::loginAction');
    $routes->get('logout', 'LoginController::logoutAction', ['as' => 'logout']);

    // Register - redirige vers notre page personnalisée
    $routes->get('register', 'RegisterController::registerView', ['as' => 'register']);
    $routes->post('register', 'RegisterController::registerAction');
});

// Routes Shield supplémentaires (magic-link, etc.) - gardent le comportement par défaut
$routes->group('', function ($routes) {
    if (setting('Auth.allowMagicLinkLogins')) {
        $routes->get('login/magic-link', '\CodeIgniter\Shield\Controllers\MagicLinkController::loginView', ['as' => 'magic-link']);
        $routes->post('login/magic-link', '\CodeIgniter\Shield\Controllers\MagicLinkController::loginAction');
        $routes->get('login/verify-magic-link', '\CodeIgniter\Shield\Controllers\MagicLinkController::verify', ['as' => 'verify-magic-link']);
    }

    // Routes pour les actions Shield (activation email, 2FA, etc.)
    // Actives uniquement si register OU login ont des actions configurées
    $actions = config('Auth')->actions;
    if ($actions['register'] !== null || $actions['login'] !== null) {
        $routes->get('auth/a/show', '\CodeIgniter\Shield\Controllers\ActionController::show', ['as' => 'auth-action-show']);
        $routes->post('auth/a/handle', '\CodeIgniter\Shield\Controllers\ActionController::handle', ['as' => 'auth-action-handle']);
        $routes->post('auth/a/verify', '\CodeIgniter\Shield\Controllers\ActionController::verify', ['as' => 'auth-action-verify']);
    }
});