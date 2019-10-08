<?php

$router = app('Shea\Component\Routing\Router'); 

$router->get('/index', 'dsa');
$router->get('/test/{id}/{s}', 'IndexController@index');