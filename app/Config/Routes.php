<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index'); //Home Page
$routes->get('/about', 'Home::about'); //About Page
$routes->get('/contact', 'Home::contact'); //Contact Page
