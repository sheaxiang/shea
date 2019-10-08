<?php

namespace Shea\Component\Routing\Matching;

use Shea\Component\Http\Request;
use Shea\Component\Routing\Route;

class UriValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/'.$request->path();
       
        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }
}
