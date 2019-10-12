<?php

return [
    'name' => env('APP_NAME', 'Shea'),

    'debug' => env('APP_DEBUG', false),

    'providers' => [
        Shea\Component\Database\DatabaseServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],

    'aliases' => [
        'App' => Shea\Component\Support\Facades\App::class,
        'Route' => Shea\Component\Support\Facades\Route::class,
        'DB' => Shea\Component\Support\Facades\DB::class,
    ],
];