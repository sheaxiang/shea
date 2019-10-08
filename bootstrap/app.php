<?php

$app = new Shea\App(
    dirname(__DIR__)
);

$app->singleton(
    Shea\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Shea\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
