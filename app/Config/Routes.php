<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->add('/login', 'Home::login');
$routes->add('/logout', 'Home::logout');
$routes->get('/inicioCajero', 'Home::inicioCajero', ['filter' => 'authGuard']);
$routes->get('/inicio', 'Home::inicio', ['filter' => 'authGuard']);
$routes->get('/cliente', 'Home::cliente', ['filter' => 'authGuard']);
$routes->get('/usuario', 'Home::usuario', ['filter' => 'authGuard']);

$routes->add('/(:any)/add', 'Home::$1/add', ['filter' => 'authGuard']);
$routes->add('(:any)/insert', 'Home::$1/insert', ['filter' => 'authGuard']);
$routes->add('(:any)/insert_validation', 'Home::$1/insert_validation', ['filter' => 'authGuard']);
$routes->add('(:any)/success/:num','Home::$1/success', ['filter' => 'authGuard']);
$routes->add('(:any)/delete/:num','Home::$1/delete', ['filter' => 'authGuard']);
$routes->add('(:any)/edit/:num','Home::$1/edit', ['filter' => 'authGuard']);
$routes->add('(:any)/update_validation/:num', 'Home::$1/update_validation', ['filter' => 'authGuard']);
$routes->add('(:any)/update/:num','Home::$1/update', ['filter' => 'authGuard']);
$routes->add('(:any)/ajax_list_info','Home::$1/ajax_list_info', ['filter' => 'authGuard']);
$routes->add('(:any)/ajax_list','Home::$1/ajax_list', ['filter' => 'authGuard']);
$routes->add('(:any)/read/:num','Home::$1/read', ['filter' => 'authGuard']);
$routes->add('(:any)/export','Home::$1/export', ['filter' => 'authGuard']);

$routes->add('/recargarTarjeta/(:any)', 'Home::recargarTarjeta/$1', ['filter'=>'authGuard']);
$routes->add('/cobrarTarjeta/(:any)', 'Home::cobrarTarjeta/$1', ['filter'=>'authGuard']);
$routes->add('/anularTarjeta/(:any)', 'Home::anularTarjeta/$1', ['filter'=>'authGuard']);
//funciones para imprimir boleta
$routes->add('/imprimirRecarga/(:any)', 'Home::imprimirRecarga/$1', ['filter'=>'authGuard']);
$routes->add('/imprimirCobro/(:any)', 'Home::imprimirCobro/$1', ['filter'=>'authGuard']);
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
