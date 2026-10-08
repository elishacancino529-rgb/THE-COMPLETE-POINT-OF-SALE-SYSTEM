<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);
$routes->get('media/(:num)', 'Media::show/$1', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    foreach (['products' => 'Products', 'customers' => 'Customers', 'staff' => 'Staff'] as $path => $controller) {
        $routes->get($path, "$controller::index");
        $routes->get("$path/new", "$controller::form");
        $routes->post($path, "$controller::save");
        $routes->get("$path/(:num)/edit", "$controller::form/$1");
        $routes->post("$path/(:num)", "$controller::save/$1");
        $routes->post("$path/(:num)/delete", "$controller::delete/$1");
    }
    $routes->get('sales', 'Sales::index');
    $routes->get('sales/new', 'Sales::form');
    $routes->post('sales', 'Sales::record');
});
