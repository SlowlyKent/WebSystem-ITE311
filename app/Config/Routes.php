<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Dashboards Routes
$routes->get('dashboard', 'Auth::dashboard');

// Course Enrollment Route
$routes->post('/course/enroll', 'Course::enroll');

// Teacher Routes
$routes->get('teacher/courses', 'Teacher::courses');
$routes->get('teacher/courses/view/(:num)', 'Material::index/$1');

// Admin Routes
$routes->get('admin/courses', 'Admin::courses');
$routes->get('admin/courses/view/(:num)', 'Material::index/$1');

// Student Routes
$routes->get('student/enrollments', 'Student::enrollments');
$routes->get('student/courses/view/(:num)', 'Material::index/$1');

// Material management
$routes->get('/materials/course/(:num)', 'Material::index/$1');
$routes->get('/materials/upload/(:num)', 'Material::uploadForm/$1');
$routes->post('/materials/upload', 'Material::upload');
$routes->get('/materials/delete/(:num)', 'Material::delete/$1');
$routes->get('/materials/download/(:num)', 'Material::download/$1');

// Notification Routes
$routes->get('/notifications', 'Notification::get');
$routes->post('/notifications/mark_read/(:num)', 'Notification::mark_as_read/$1');
$routes->post('/notifications/mark_all', 'Notification::mark_all');

