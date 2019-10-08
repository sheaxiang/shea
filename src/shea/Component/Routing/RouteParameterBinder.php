<?php

namespace Shea\Component\Routing;

class RouteParameterBinder
{
    protected $route;

    public function __construct($route)
    {
        $this->route = $route;
    }

    public function parameters($request)
    {
        $parameters = $this->bindPathParameters($request);

        //todo
        return $parameters;
    }

    protected function bindPathParameters($request)
    {
        $path = '/'.ltrim($request->decodedPath(), '/');

        preg_match($this->route->compiled->getRegex(), $path, $matches);
        
        return $this->matchToKeys(array_slice($matches, 1));
    }

    protected function matchToKeys(array $matches)
    {
        //寻找{}参数
        if (empty($parameterNames = $this->route->parameterNames())) {
            return [];
        }

        $parameters = array_intersect_key($matches, array_flip($parameterNames));
        
        return array_filter($parameters, function ($value) {
            return is_string($value) && strlen($value) > 0;
        });
    }
}
