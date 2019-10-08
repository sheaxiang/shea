<?php

namespace Shea\Component\Routing\Matching;

use Shea\Component\Http\Request;
use Shea\Component\Routing\Route;

class MethodValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        return in_array($request->getMethod(), $route->methods());
    }
}
