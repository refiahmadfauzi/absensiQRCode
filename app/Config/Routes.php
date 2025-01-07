<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->post('/loginAction', 'Login::login');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/logout', 'Dashboard::logout');
$routes->get('/qrcode/(:num)', 'Dashboard::generate/$1');
$routes->post('/qrcode/processScan', 'Dashboard::processScan');



$routes->get('/user', 'User::index');
$routes->post('/user/create', 'User::create');
$routes->post('/user/update', 'User::update');
$routes->get('/user/getbyid/(:any)', 'User::getbyid/$1');
$routes->post('/user/delete/(:any)', 'User::delete/$1');

$routes->get('/absensi', 'Absensi::index');
