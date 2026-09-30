<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── 1. Log Masuk, Log Keluar & Dummy Login ──────────────────────────
$routes->get('login',                            'AuthController::index');
$routes->post('login',                           'AuthController::login');
$routes->get('logout',                           'AuthController::logout');
$routes->get('auth/dummy/(:segment)/(:num)',     'AuthController::dummyLogin/$1/$2');
$routes->get('auth/dummy/(:segment)',            'AuthController::dummyLogin/$1');

// ── 2. Integrasi Pelancar iPesakit (Launch URL) ────────────────────
$routes->get('launch',                           'IntegrationController::launch');

// ── 3. Pengesahan Awam QR Code & Cetakan Dokumen (Tanpa Sekatan Log Masuk) ──
$routes->get('verify/mc/(:any)',                 'VerifyController::mc/$1');
$routes->get('verify/timeslip/(:any)',           'VerifyController::timeslip/$1');
$routes->get('mc/view/(:num)',                   'McController::view/$1');
$routes->get('timeslip/view/(:num)',             'TimeSlipController::view/$1');

// ── 4. Panel Pentadbir Jabatan IT (Terhad kepada Admin IT) ─────────
$routes->group('admin', ['filter' => 'admin'], static function($routes) {
    $routes->get('/',                            'AdminController::index');
    $routes->get('doctors',                      'AdminController::doctors');
    $routes->post('doctors/save',                'AdminController::saveDoctor');
    $routes->get('doctors/toggle/(:num)',        'AdminController::toggleDoctor/$1');
    $routes->get('api-status',                   'AdminController::apiStatus');
    $routes->get('audit-log',                    'AdminController::auditLog');
});

// ── 5. Modul Dalaman Sistem eMC (Dilindungi oleh AuthFilter) ────────
$routes->group('', ['filter' => 'auth'], static function($routes) {
    // Dashboard
    $routes->get('/',                            'Home::index');
    $routes->get('dashboard/chartData',          'Home::chartData');
    $routes->get('dashboard/searchPatient',      'Home::searchPatient');

    $routes->get('testApi/(:num)',               'PatientController::testApi/$1');

    // Patients — hanya route select dikekalkan (digunakan oleh IntegrationController)
    // Route index, create, store dibuang — akses wajib melalui iPesakit
    $routes->get('patients',                     'PatientController::accessDenied');
    $routes->get('patients/create',              'PatientController::accessDenied');
    $routes->get('patients/select/(:any)',       'PatientController::select/$1');

    // Medical Documents hub
    $routes->get('documents',                    'DocumentsController::index');

    // Patient History — search by RN
    $routes->get('patient-history',              'PatientHistoryController::index');

    // Medical Certificate
    $routes->get('mc',                           'McController::index');
    $routes->get('mc/create/(:any)',             'McController::create/$1');
    $routes->post('mc/store',                    'McController::store');
    $routes->get('mc/edit/(:num)',               'McController::edit/$1');
    $routes->post('mc/update/(:num)',            'McController::update/$1');

    // Time Slip
    $routes->get('timeslip',                     'TimeSlipController::index');
    $routes->get('timeslip/create/(:any)',       'TimeSlipController::create/$1');
    $routes->post('timeslip/store',              'TimeSlipController::store');
    $routes->get('timeslip/edit/(:num)',         'TimeSlipController::edit/$1');
    $routes->post('timeslip/update/(:num)',      'TimeSlipController::update/$1');
});
