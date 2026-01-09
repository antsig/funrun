<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
// cek status
$routes->get('/cek-status', 'Status::index');
$routes->post('/cek-status', 'Status::check');
// midtrans
$routes->post('/callback/midtrans', 'Callback::midtrans');

// Registration Flow
$routes->get('/registration', 'Registration::index');
$routes->post('/registration/add', 'Registration::add');
$routes->get('/registration/remove/(:num)', 'Registration::remove/$1');
$routes->get('/registration/decrease/(:num)', 'Registration::decrease/$1');

// Checkout
$routes->get('/checkout', 'Checkout::index');
$routes->post('/checkout/process', 'Checkout::process');

// Payment
$routes->get('/payment/print/(:segment)', 'Payment::print/$1');
$routes->get('/payment/edit/(:segment)', 'Payment::edit/$1');
$routes->get('/payment/(:segment)', 'Payment::index/$1');
$routes->post('/payment/manual-confirm/(:segment)', 'ManualPayment::upload/$1');

// Admin Panel
// Admin Panel
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    // Auth Routes (Public)
    $routes->get('login', 'Auth::login');
    $routes->post('process-login', 'Auth::processLogin');
    $routes->get('verify-otp', 'Auth::verifyOtp');
    $routes->post('process-otp', 'Auth::processOtp');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('process-forgot', 'Auth::processForgot');
    $routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
    $routes->post('process-reset', 'Auth::processReset');

    // Protected Routes
    $routes->group('', ['filter' => 'adminAuth'], function ($routes) {
        $routes->get('/', function () {
            return redirect()->to('/admin/dashboard');
        });
        $routes->get('dashboard', 'Dashboard::index');

        // Orders
        $routes->get('orders', 'Orders::index');
        $routes->get('orders/show/(:num)', 'Orders::show/$1');
        $routes->post('orders/updateStatus/(:num)', 'Orders::updateStatus/$1');
        $routes->get('orders/approvePayment/(:num)', 'Orders::approvePayment/$1');
        $routes->get('orders/rejectPayment/(:num)', 'Orders::rejectPayment/$1');

        // Race Kit Collection
        $routes->group('racekit', function ($routes) {
            $routes->get('/', 'RaceKit::index');
            $routes->get('search', 'RaceKit::search');
            $routes->get('detail/(:segment)', 'RaceKit::detail/$1');
            $routes->match(['get', 'post'], 'mark/(:num)', 'RaceKit::markCollected/$1');
            $routes->get('mark-all/(:num)', 'RaceKit::markAllCollected/$1');
        });

        // My Profile
        $routes->get('profile', 'Profile::index');
        $routes->post('profile/update', 'Profile::update');

        // User Management
        // User Management (Protected: Administrator only)
        // User Management (Protected: Administrator only)
        $routes->group('', ['filter' => 'role:administrator'], function ($routes) {
            // Events
            $routes->get('events', 'Events::index');
            $routes->get('events/create', 'Events::create');
            $routes->post('events/store', 'Events::store');
            $routes->get('events/edit/(:num)', 'Events::edit/$1');
            $routes->post('events/update/(:num)', 'Events::update/$1');
            $routes->post('events/addCategory/(:num)', 'Events::addCategory/$1');
            $routes->get('events/deleteCategory/(:num)', 'Events::deleteCategory/$1');
            $routes->get('events/tickets/(:num)', 'Events::tickets/$1');

            $routes->get('users', 'Users::index');
            $routes->get('users/create', 'Users::create');
            $routes->post('users/store', 'Users::store');
            $routes->get('users/edit/(:num)', 'Users::edit/$1');
            $routes->post('users/update/(:num)', 'Users::update/$1');
            $routes->get('users/delete/(:num)', 'Users::delete/$1');

            // Settings (Protected: Administrator only)
            $routes->get('settings', 'Settings::index');
            $routes->post('settings/save', 'Settings::save');
            $routes->get('settings/test-email', 'Settings::testEmail');
        });
    });
});
