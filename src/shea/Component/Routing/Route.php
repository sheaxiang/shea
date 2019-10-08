<?php

namespace Shea\Component\Routing;

use ReflectionFunction;
use Shea\Component\Http\Exceptions\HttpResponseException;
use Shea\Component\Http\Request;
use Shea\Component\Routing\Matching\MethodValidator;
use Shea\Component\Routing\Matching\UriValidator;
use Shea\Component\Support\Str;
use Shea\Container;

class Route
{
    public $uri;

    public $methods;

    public $action;

    public $compiled;

    protected $router;

    protected $container;

    public $parameterNames;

    public $controller;
    

    public static $validators;//路由验证器

    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = $action;

        if (isset($this->action['prefix'])) {
            $this->prefix($this->action['prefix']);
        }
    }

    public function run()
    {
        $this->container = $this->container ?: new Container;

        try {
            if ($this->isControllerAction()) {
                return $this->runController();
            }

            return $this->runCallable();
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    protected function runController()
    {
        return $this->controllerDispatcher()->dispatch(
            $this, $this->getController(), $this->getControllerMethod()
        );
    }

    public function getController()
    {
        if (! $this->controller) {
            $class = $this->parseControllerCallback()[0];

            $this->controller = $this->container->make(ltrim($class, '\\'));
        }

        return $this->controller;
    }

    protected function getControllerMethod()
    {
        return $this->parseControllerCallback()[1];
    }

    protected function parseControllerCallback()
    {
        return Str::parseCallback($this->action['uses']);
    }

    public function controllerDispatcher()
    {
        return new ControllerDispatcher($this->container);
    }

    protected function isControllerAction()
    {
        return is_string($this->action['uses']);
    }

    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function ($p) {
            return ! is_null($p);
        });
    }

    protected function runCallable()
    {
        $callable = $this->action['uses'];

        return $callable(...array_values($this->resolveMethodDependencies(
            $this->parametersWithoutNulls(), new ReflectionFunction($this->action['uses'])
        )));
    }

    public function parameters()
    {
        if (isset($this->parameters)) {
            return $this->parameters;
        }

        throw new LogicException('Route is not bound.');
    }

    public function uri()
    {
        return $this->uri;
    }

    public function methods()
    {
        return $this->methods;
    }

    public function getCompiled()
    {
        return $this->compiled;
    }

    public function bind(Request $request)
    {
        $this->compileRoute();

        $this->parameters = (new RouteParameterBinder($this))
                        ->parameters($request);
                        
        return $this;
    }

    /**
     * 匹配路由参数
     */
    public function parameterNames()
    {
        if (isset($this->parameterNames)) {
            return $this->parameterNames;
        }

        return $this->parameterNames = $this->compileParameterNames();
    }

    protected function compileParameterNames()
    {
        preg_match_all('/\{(.*?)\}/', $this->uri, $matches);

        return array_map(function ($m) {
            return trim($m, '?');
        }, $matches[1]);
    }

    
    public function matches(Request $request)
    {
        //路由匹配规则
        $this->compileRoute();

        foreach ($this->getValidators() as $validator) {
            if (! $validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    protected function compileRoute()
    {
        if (! $this->compiled) {
            //开始使用symfony的路由组件
            $this->compiled = (new RouteCompiler($this))->compile();
        }

        return $this->compiled;
    }

    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        return static::$validators = [
            new UriValidator, new MethodValidator
        ];
    }

    public function prefix($prefix)
    {
        //todo
        $uri = rtrim($prefix, '/').'/'.ltrim($this->uri, '/');

        $this->uri = trim($uri, '/');

        return $this;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}