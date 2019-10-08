<?php

namespace Shea\Contracts\Routing;

use Shea\Component\Routing\Route;

interface ControllerDispatcher
{
    public function dispatch(Route $route, $controller, $method);

    public function getMiddleware($controller, $method);
}
