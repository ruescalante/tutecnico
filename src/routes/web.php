<?php

require_once BASE_PATH . '/middleware/AuthMiddleware.php';

#Home
$router->get('/', 'HomeController@index');

#Ejemplo (protegidas con AuthMiddleware para pruebas)
$auth = ['AuthMiddleware'];
$router->get('/ejemplo', 'EjemploController@index');
$router->post('/ejemplo', 'EjemploController@store');
$router->get('/ejemplo/create', 'EjemploController@create', $auth);
$router->get('/ejemplo/edit', 'EjemploController@edit', $auth);
$router->post('/ejemplo/update', 'EjemploController@update', $auth);
$router->post('/ejemplo/delete', 'EjemploController@destroy', $auth);
