<?php

namespace Shea;

use Closure;
use ArrayAccess;
use ErrorException;
use ReflectionClass;
use ReflectionParameter;
use Shea\Contracts\Container\Container as ContainerContract;

/**
 * laravel不支持路由前注入function
 * 
 * 容器类
 */

class Container implements ArrayAccess, ContainerContract
{
    /**
     * 容器对象实例
     */
    protected static $instance;

    protected $instances;

    protected $bind;
    
    /**
     * 获取当前容器实例
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        if (static::$instance instanceof Closure) {
            return (static::$instance)();
        }

        return static::$instance;
    }

    public static function setInstance($instance)
    {
        static::$instance = $instance;   
    }

    public function get($id)
    {
        try {
            return $this->make($id);
        } catch (Exception $e) {
            if ($this->has($id)) {
                throw $e;
            }

            throw new EntryNotFoundException;
        }
    }

    public function make($abstract, $parameters = [], bool $newInstance = false)
    {
        $concrete = $this->getAlias($abstract);
        
        if (isset($this->instances[$abstract]) && !$newInstance) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        //构建
        $object = $this->build($concrete, $parameters);

        if (!$newInstance) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function build($concrete, $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);   
        }

        $reflector = new ReflectionClass($concrete);

        //检测类是否可实例化
        if (! $reflector->isInstantiable()) {
            throw new ErrorException("$concrete is not instantiable.");
        }

        //获取构造函数
        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }
        
        //获取参数依赖
        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies(
            $dependencies
        );

        return $reflector->newInstanceArgs($instances);
    }

    protected function getConcrete($abstract)
    {
        if (isset($this->bind[$abstract])) {
            return $this->bind[$abstract];
        }

        return $abstract;
    }

    protected function resolveDependencies(array $dependencies) 
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            
            //如果这个参数无法获取类,将解析普通参数
            $results[] = is_null($dependency->getClass())
                        ? $this->resolvePrimitive($dependency)
                        : $this->resolveClass($dependency); 
        }

        return $results;
    }

    public function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }

    public function resolvePrimitive(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ErrorException("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}");
    }

    /**
     * 根据别名获取真实类名
     */
    public function getAlias($abstract)
    {
        if (!isset($this->bind[$abstract])) {
            return $abstract;
        }

        return $this->bind[$abstract];
    }

    /**
     * 绑定一个类实例到容器
     */
    public function instance($abstract, $instance)
    {
        $abstract = $this->getAlias($abstract);
        
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /**
     * 向容器中注册绑定
     */
    public function bind($abstract, $concrete = null)
    {
        //先删除旧的实例
        //$this->dropStaleInstances($abstract);
        if (! $concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        //todo 
        $this->bind[$abstract] = $concrete;

        return $this;
    }

    protected function getClosure($abstract, $concrete)
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            return $container->make($concrete, $parameters);
        };
    }

    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract]);
    }

    public function has($id)
    {
        return $this->bound($id);
    }

    public function bound($abstract)
    {
        return isset($this->bind[$abstract]) || isset($this->instances[$abstract]);
    }

    public function factory($abstract)
    {
        return function () use ($abstract) {
            return $this->make($abstract);
        };
    }

    public function offsetExists($key)
    {
        return $this->bound($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->bind[$key], $this->instances[$key]);
    }

    public function __get($key)
    {
        return $this[$key];
    }

    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}