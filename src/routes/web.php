<?php

#Home
$router->get('/', 'HomeController@index');

#Ejemplo
$router->get('/ejemplo', 'EjemploController@index');
$router->post('/ejemplo', 'EjemploController@store');
$router->get('/ejemplo/create',     'EjemploController@create');
$router->get('/ejemplo/edit',       'EjemploController@edit');
$router->post('/ejemplo/update',    'EjemploController@update');
$router->post('/ejemplo/delete',    'EjemploController@destroy');
