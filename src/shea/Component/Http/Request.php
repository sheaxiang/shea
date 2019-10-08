<?php

namespace Shea\Component\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest 
{
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();
        
        //这里需返回本实例
        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * create Shea request form SymfonyRequest
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) {
            return $request;
        }

        $content = $request->content;

        $request = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $request->content = $content;

        $request->request = $request->getInputSource();

        return $request;
    }

    protected function getInputSource()
    {
        //todo 
        return in_array($this->getRealMethod(), ['GET', 'HEAD']) ? $this->query : $this->request;
    }

    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        return parent::duplicate($query, $request, $attributes, $cookies, $files, $server);//todo $files
    }

    public function instance()
    {
        return $this;
    }

    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern == '' ? '/' : $pattern;
    }

    public function decodedPath()
    {
        return rawurldecode($this->path());
    }
}