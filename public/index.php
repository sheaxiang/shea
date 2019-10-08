<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Shea\Contracts\Http\Kernel::class);
    
$response = $kernel->handle(
    $request = Shea\Component\Http\Request::capture()
);

$response->send();

