<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

//这里是个interface,不能直接实例化,但前面已进行对外绑定,可实例化对应类
$kernel = $app->make(Shea\Contracts\Http\Kernel::class);
    
//处理请求
$response = $kernel->handle(
    //使用Symfony的HttpFoundation包创建一个http请求
    $request = Shea\Component\Http\Request::capture()
);

$response->send();

