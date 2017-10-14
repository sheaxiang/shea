<?php

// 定义 PUBLIC_PATH

define('PUBLIC_PATH', __DIR__);

// 启动器
require PUBLIC_PATH . '/../system/bootstrap.php';

// 路由配置
require '../config/routes.php';