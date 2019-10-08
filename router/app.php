<?php

$router = app('Shea\Component\Routing\Router'); 

$router->get('/', 'IndexController@index');