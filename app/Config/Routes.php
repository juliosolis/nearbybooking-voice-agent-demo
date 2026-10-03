<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Demo::index');
$routes->post('simulate', 'Demo::simulate', ['filter' => 'csrf']);
$routes->get('admin', 'Admin::index', ['filter' => 'session']);
$routes->post('admin/faq/(:num)', 'Admin::saveFaq/$1', ['filter' => ['session', 'csrf']]);
$routes->post('admin/request/(:num)', 'Admin::reviewRequest/$1', ['filter' => ['session', 'csrf']]);
$routes->post('voice/incoming', 'Voice::incoming');
$routes->post('voice/turn', 'Voice::turn');

service('auth')->routes($routes);
