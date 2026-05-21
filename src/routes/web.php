<?php

require_once BASE_PATH . '/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/middleware/AdminMiddleware.php';
require_once BASE_PATH . '/middleware/TechnicianMiddleware.php';

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

#Ejemplo (protegidas con AuthMiddleware para pruebas)
$router->get('/ejemplo', 'EjemploController@index', ['AuthMiddleware']);
$router->post('/ejemplo', 'EjemploController@store', ['AuthMiddleware']);
$router->get('/ejemplo/create', 'EjemploController@create', ['AuthMiddleware']);
$router->get('/ejemplo/edit/:id', 'EjemploController@edit', ['AuthMiddleware']);
$router->post('/ejemplo/:id', 'EjemploController@update', ['AuthMiddleware']);
$router->post('/ejemplo/delete/:id', 'EjemploController@destroy', ['AuthMiddleware']);
