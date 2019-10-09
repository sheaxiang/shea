<?php

return [
    'name' => env('APP_NAME', 'Shea'),

    'debug' => env('APP_DEBUG', false),

    'aliases' => [
        'App' => Shea\Component\Support\Facades\App::class,
        'Route' => Shea\Component\Support\Facades\Route::class,
    ],
];