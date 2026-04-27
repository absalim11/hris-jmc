<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // Auth
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/refresh', 'AuthController::refresh');
    $routes->get('auth/me', 'AuthController::me');
    
    // Dashboard
    $routes->get('dashboard/manager', 'DashboardController::manager');
    $routes->get('dashboard', 'DashboardController::index');
    // RBAC matrix + toggle
    $routes->get('rbac/matrix', 'RbacController::matrix');
    $routes->post('rbac/toggle', 'RbacController::toggle', ['filter' => 'rbac:role,update']);

    // ROLES
    $routes->group('roles', ['namespace' => 'App\Controllers\Api', 'filter' => 'rbac:role,read'], function($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->post('/', 'RoleController::create', ['filter' => 'rbac:role,create']);
        $routes->put('(:num)', 'RoleController::update/$1', ['filter' => 'rbac:role,update']);
        $routes->delete('(:num)', 'RoleController::delete/$1', ['filter' => 'rbac:role,delete']);
    });

    // USERS
    $routes->group('users', ['namespace' => 'App\Controllers\Api', 'filter' => 'rbac:user,read'], function($routes) {
        $routes->get('/', 'UserController::index');
        $routes->post('/', 'UserController::create', ['filter' => 'rbac:user,create']);
        $routes->put('(:num)', 'UserController::update/$1', ['filter' => 'rbac:user,update']);
        $routes->delete('(:num)', 'UserController::delete/$1', ['filter' => 'rbac:user,delete']);
        $routes->put('(:num)/status', 'UserController::toggleStatus/$1', ['filter' => 'rbac:user,update']);
    });

    // PEGAWAI
    $routes->group('pegawai', ['namespace' => 'App\Controllers\Api', 'filter' => 'rbac:pegawai,read'], function($routes) {
        $routes->get('/', 'PegawaiController::index');
        $routes->post('/', 'PegawaiController::create', ['filter' => 'rbac:pegawai,create']);
        $routes->put('(:num)', 'PegawaiController::update/$1', ['filter' => 'rbac:pegawai,update']);
        $routes->delete('(:num)', 'PegawaiController::delete/$1', ['filter' => 'rbac:pegawai,delete']);
        $routes->get('autocomplete', 'PegawaiController::getAutocomplete');
        $routes->get('export/pdf', 'PegawaiController::exportPdf');
        $routes->get('(:num)', 'PegawaiController::show/$1');
        $routes->post('(:num)/foto', 'PegawaiController::uploadFoto/$1', ['filter' => 'rbac:pegawai,update']);

        // Pegawai pendidikan (dynamic list)
        $routes->get('(:num)/pendidikan', 'PegawaiPendidikanController::index/$1');
        $routes->post('(:num)/pendidikan', 'PegawaiPendidikanController::create/$1', ['filter' => 'rbac:pegawai,create']);
        $routes->put('pendidikan/(:num)', 'PegawaiPendidikanController::update/$1', ['filter' => 'rbac:pegawai,update']);
        $routes->delete('pendidikan/(:num)', 'PegawaiPendidikanController::delete/$1', ['filter' => 'rbac:pegawai,delete']);
    });

    // TUNJANGAN
    $routes->group('tunjangan', ['namespace' => 'App\Controllers\Api', 'filter' => 'rbac:tunjangan,read'], function($routes) {
        $routes->get('/', 'TunjanganController::index');
        $routes->post('/', 'TunjanganController::create', ['filter' => 'rbac:tunjangan,create']);
        $routes->post('calculate', 'TunjanganController::calculatePreview', ['filter' => 'rbac:tunjangan,create']);
    });

    // SETTING TUNJANGAN
    $routes->group('setting-tunjangan', ['namespace' => 'App\Controllers\Api', 'filter' => 'rbac:setting_tunjangan,read'], function($routes) {
        $routes->get('/', 'SettingTunjanganController::index');
        $routes->post('/', 'SettingTunjanganController::create', ['filter' => 'rbac:setting_tunjangan,create']);
        $routes->get('aktif', 'SettingTunjanganController::getAktif');
    });

    // ACTIVITY LOGS
    $routes->group('logs', ['namespace' => 'App\\Controllers\\Api', 'filter' => 'rbac:activity_log,read'], function($routes) {
        $routes->get('/', 'ActivityLogController::index');
        $routes->get('stats', 'ActivityLogController::stats');
        $routes->get('(:num)', 'ActivityLogController::show/$1');
    });

    // WILAYAH (No RBAC, just namespace)
    $routes->get('wilayah/kecamatan', 'WilayahController::getKecamatan');
    $routes->get('wilayah/kabupaten/(:num)', 'WilayahController::getKabupaten/$1');
    $routes->get('wilayah/provinsi/(:num)', 'WilayahController::getProvinsi/$1');
});
