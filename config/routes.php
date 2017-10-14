<?php

use NoahBuscher\Macaw\Macaw;

Macaw::get('/index','App\Http\Controllers\IndexController@index');

Macaw::get('/', function() {
    echo "成功！";
    exit;
});

Macaw::error(function() {
    throw new Exception("路由无匹配项 404 Not Found");
});

Macaw::dispatch();