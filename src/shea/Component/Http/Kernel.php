<?php

namespace Shea\Component\Http;

use Shea\App;
use Shea\Component\Routing\Router;
use Shea\Contracts\Http\Kernel as SheaKernel;

class Kernel implements SheaKernel
{
    protected  $app;

    protected $router;

    protected $bootstrappers = [
        //todo 
        \Shea\Bootstrap\LoadEnvironmentVariables::class,
        \Shea\Bootstrap\LoadConfiguration::class,
        \Shea\Bootstrap\HandleExceptions::class,
        \Shea\Bootstrap\LoadRouter::class,
    ];

    public function __construct(App $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    public function handle($request)
    {
        try {
            $request->enableHttpMethodParameterOverride();
            
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            //todo 响应http
            echo $e->getMessage(); 
            return;
        } catch (Throwable $e) {
            echo $e->getMessage(); 
            return;
        }

        return $response;
    }

    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);

        $this->bootstrap();

        return $this->router->dispatchToRoute($request);
    }

    public function terminate($request, $response)
    {
       
    }

    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    public function getApplication()
    {
        return $this->app;
    }
}