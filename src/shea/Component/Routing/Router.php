<?php

namespace Shea\Component\Routing;

use Closure;
use ArrayObject;
use JsonSerializable;
use Shea\Component\Http\Request;
use Shea\Component\Http\Response;
use Shea\Container;
use Shea\Contracts\Support\Arrayable;
use Shea\Contracts\Support\Jsonable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Router
{
    protected $container;

    protected $routes;

    protected $groupStack;

    public function __construct(RouteCollection $routes, Container $container)
    {
        $this->routes = $routes;
        $this->container = $container;
    }

    public function get($uri, $action = null)
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public function addRoute($methods, $uri, $action)
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    protected function createRoute($methods, $uri, $action)
    {
        //判断路由是否是controller,检测是否为闭包
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }

        $route = $this->newRoute(
            $methods, $this->prefix($uri), $action
        );
        
        return $route;
    }

    protected function prefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
    }

    public function getLastGroupPrefix()
    {
        if (! empty($this->groupStack)) {
            $last = end($this->groupStack);

            return $last['prefix'] ?? '';
        }

        return '';
    }

    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
                    ->setRouter($this)
                    ->setContainer($this->container);
    }

    protected function convertToControllerAction($action)
    {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }
        
        if (! empty($this->groupStack)) {
            $action['uses'] = $this->prependGroupNamespace($action['uses']);
        }
        
        $action['controller'] = $action['uses'];

        return $action;
    }

    protected function prependGroupNamespace($class)
    {
        $group = end($this->groupStack);
        
        return isset($group['namespace']) && strpos($class, '\\') !== 0
                ? $group['namespace'].'\\'.$class : $class;
    }

    protected function actionReferencesController($action)
    {
        if (! $action instanceof Closure) {
            return is_string($action) || (isset($action['uses']) && is_string($action['uses']));
        }

        return false;
    }

    public function updateGroupStack(array $attributes)
    {
        $this->groupStack[] = $attributes;
    }
    
    /**
     * 将请求发送到路由并返回
     */
    public function dispatchToRoute($request)
    {
        return $this->runRoute($request, $this->findRoute($request));
    }

    public function findRoute($request)
    {
        $route = $this->routes->match($request);

        $this->container->instance(Route::class, $route);

        return $route;
    }

    protected function runRoute(Request $request, Route $route)
    {
        return $this->prepareResponse(
            $request, $route->run()
        );
    }

    public function prepareResponse($request, $response)
    {
        if (! $response instanceof SymfonyResponse &&
                   ($response instanceof Arrayable ||
                    $response instanceof Jsonable ||
                    $response instanceof ArrayObject ||
                    $response instanceof JsonSerializable ||
                    is_array($response))) {
            $response = new JsonResponse($response);
        } elseif (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response->prepare($request);
    }

    public function __call($name, $arguments)
    {
        dd('call:'.$name);
    }
}