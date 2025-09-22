<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//Lab #3 MVC- Routing Configuration 
$routes->get('/home', 'Home::index'); //Home Page
$routes->get('/about', 'Home::about'); //About Page
$routes->get('/contact', 'Home::contact'); //Contact Page

// Authentication Routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');
