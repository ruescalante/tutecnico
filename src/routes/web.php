<?php

require_once BASE_PATH . '/middleware/AuthMiddleware.php';

#Home
$router->get('/', 'HomeController@index');

#Ejemplo (protegidas con AuthMiddleware para pruebas)
#$auth = ['AuthMiddleware'];
#Ejemplo auth middleware $router->get('/ejemplo', 'EjemploController@index',['AuthMiddleware']);
$router->get('/ejemplo', 'EjemploController@index');
$router->post('/ejemplo', 'EjemploController@store');
$router->get('/ejemplo/create', 'EjemploController@create');
$router->get('/ejemplo/edit', 'EjemploController@edit');
$router->post('/ejemplo/update', 'EjemploController@update');
$router->post('/ejemplo/delete', 'EjemploController@destroy');
