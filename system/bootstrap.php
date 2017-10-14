<?php
use Illuminate\Database\Capsule\Manager as Capsule;

//自动加载
require __DIR__ . '/../vendor/autoload.php';

//错误提示
$whoops = new \Whoops\Run;

$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

$whoops->register();

// Eloquent ORM
$capsule = new Capsule;

$capsule->addConnection(require __DIR__ . '/../config/databases.php');

$capsule->bootEloquent();


