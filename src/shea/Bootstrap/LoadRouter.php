<?php

namespace Shea\Bootstrap;

use Shea\App;
use Shea\Component\Config\Repository;
use Shea\Component\Routing\Router;
use Symfony\Component\Finder\Finder;

class LoadRouter
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    public function bootstrap(App $app)
    {
       //todo  缓存

        $this->loadRouterFiles($app);
    }

    protected function loadRouterFiles(App $app)
    {
        //返回规范化的绝对路径名
        $routerPath = realpath($app->routerPath());
        
        foreach (Finder::create()->files()->name('*.php')->in($routerPath) as $file) {
            //先写入栈
            $this->router->updateGroupStack([
                'namespace' => env('ROUTE_DEFAULT_NAMESPACE', 'App\Controllers'),
                'prefix' => env('ROUTE_DEFAULT_PREFIX', ''),
            ]);

            require $file->getRealPath();
        }
    }
}