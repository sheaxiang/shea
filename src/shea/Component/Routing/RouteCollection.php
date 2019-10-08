<?php

namespace Shea\Component\Routing;

use Shea\Component\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteCollection
{
    protected $routes = [];

    public function match($request)
    {
        $routes = $this->get($request->getMethod());
        
        //匹配路由
        $route = $this->matchAgainstRoutes($routes, $request);

        if (! is_null($route)) {
            //绑定路由参数
            return $route->bind($request);
        }

        throw new NotFoundHttpException;
    }

    protected function matchAgainstRoutes(array $routes, $request)
    {
        return Arr::first(array_filter($routes, function($value) use($request) {
            return $value->matches($request);
        })); 
    }

    public function add(Route $route)
    {
        //todo
        $this->addToCollections($route);

        return $route;
    }

    public function get($method = null)
    {
        return is_null($method) ? $this->getRoutes() : Arr::get($this->routes, $method, []);
    }

    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }

    protected function addToCollections($route)
    {
        //归纳路由,将不同请求方式归纳
        foreach ($route->methods() as $method) {
            $this->routes[$method][] = $route;
        }

        $this->allRoutes[$method] = $route;
    }
}