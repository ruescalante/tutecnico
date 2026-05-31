<?php

require_once BASE_PATH . '/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/middleware/AdminMiddleware.php';
require_once BASE_PATH . '/middleware/TechnicianMiddleware.php';
require_once BASE_PATH . '/middleware/ClientMiddleware.php';

#Home
$router->get('/', 'HomeController@index');

#Auth
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/registro', 'AuthController@showRegister');
$router->post('/registro', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');

#Dashboard y perfil
$router->get('/dashboard', 'DashboardController@index', ['AuthMiddleware']);
$router->get('/dashboard/cliente', 'DashboardController@client', ['AuthMiddleware']);
$router->get('/dashboard/tecnico', 'DashboardController@technician', ['AuthMiddleware', 'TechnicianMiddleware']);
$router->get('/dashboard/tecnico/espera', 'DashboardController@technicianWaiting', ['AuthMiddleware', 'TechnicianMiddleware']);
$router->get('/dashboard/admin', 'AdminController@index', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/dashboard/admin/usuarios', 'AdminController@usuarios', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/dashboard/admin/usuarios/:id/editar', 'AdminController@editUsuario', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/editar', 'AdminController@updateUsuario', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/suspender', 'AdminController@suspenderUsuario', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/eliminar', 'AdminController@eliminarUsuario', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/dashboard/admin/tecnicos', 'AdminController@tecnicos', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/dashboard/admin/tecnicos/:id/estado', 'AdminController@updateTechnicianStatus', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/dashboard/admin/solicitudes', 'AdminController@solicitudes', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/perfil', 'ProfileController@index', ['AuthMiddleware']);
$router->get('/perfil/editar', 'ProfileController@showEditForm', ['AuthMiddleware']);
$router->post('/perfil/editar', 'ProfileController@updateProfile', ['AuthMiddleware']);
$router->get('/perfil/solicitud-tecnico', 'ProfileController@showTechnicianForm', ['AuthMiddleware']);
$router->post('/perfil/solicitud-tecnico', 'ProfileController@applyTechnician', ['AuthMiddleware']);
#$router->post('/perfil/foto-trabajo/eliminar', 'ProfileController@deleteFotoTrabajo', ['AuthMiddleware']);

# Perfil público de técnico
$router->get('/tecnico/:id(int)', 'TecnicoController@show');
$router->post('/tecnico/:id(int)/resena', 'TecnicoController@storeResena', ['AuthMiddleware']);
$router->post('/tecnico/:id(int)/resena/editar', 'TecnicoController@editResena', ['AuthMiddleware']);
$router->post('/tecnico/:id(int)/resena/eliminar', 'TecnicoController@deleteResena', ['AuthMiddleware']);

# Solicitudes — Cliente
$router->get('/solicitudes/crear/:id(int)',                        'SolicitudController@create',      ['AuthMiddleware', 'ClientMiddleware']);
$router->post('/solicitudes/crear',                                'SolicitudController@store',       ['AuthMiddleware', 'ClientMiddleware']);
$router->get('/solicitudes/:id(int)',                              'SolicitudController@show',        ['AuthMiddleware']);
$router->post('/solicitudes/:id(int)/cancelar',                    'SolicitudController@cancel',      ['AuthMiddleware']);
$router->post('/solicitudes/:id(int)/cotizacion/aceptar',          'SolicitudController@acceptQuote', ['AuthMiddleware', 'ClientMiddleware']);
$router->post('/solicitudes/:id(int)/cotizacion/rechazar',         'SolicitudController@rejectQuote',   ['AuthMiddleware', 'ClientMiddleware']);
$router->post('/solicitudes/:id(int)/mensaje',                     'SolicitudController@sendMessage',   ['AuthMiddleware', 'ClientMiddleware']);

# Dashboard Técnico — Solicitudes
$router->get('/dashboard/tecnico/solicitudes',                     'SolicitudController@techIndex',  ['AuthMiddleware', 'TechnicianMiddleware']);
$router->get('/dashboard/tecnico/solicitudes/:id(int)',            'SolicitudController@techShow',   ['AuthMiddleware', 'TechnicianMiddleware']);
$router->post('/dashboard/tecnico/solicitudes/:id(int)/cotizar',   'SolicitudController@sendQuote',  ['AuthMiddleware', 'TechnicianMiddleware']);
$router->post('/dashboard/tecnico/solicitudes/:id(int)/rechazar',   'SolicitudController@techReject',  ['AuthMiddleware', 'TechnicianMiddleware']);
$router->post('/dashboard/tecnico/solicitudes/:id(int)/completar',  'SolicitudController@complete',      ['AuthMiddleware', 'TechnicianMiddleware']);
$router->post('/dashboard/tecnico/solicitudes/:id(int)/mensaje',    'SolicitudController@techSendMessage', ['AuthMiddleware', 'TechnicianMiddleware']);

# Notificaciones (AJAX)
$router->get('/notificaciones/recientes',      'NotificacionController@getRecent',   ['AuthMiddleware']);
$router->post('/notificaciones/marcar-leidas', 'NotificacionController@markAllRead', ['AuthMiddleware']);
