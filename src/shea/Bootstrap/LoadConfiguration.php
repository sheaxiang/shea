<?php

namespace Shea\Bootstrap;

use Exception;
use SplFileInfo;
use Shea\App;
use Shea\Component\Config\Repository;
use Symfony\Component\Finder\Finder;

class LoadConfiguration
{
    public function bootstrap(App $app)
    {
       //todo  缓存
        $items = [];

        $app->instance('config', $config = new Repository($items));

        $this->loadConfigurationFiles($app, $config);
        
        date_default_timezone_set($config->get('app.timezone', 'PRC'));

        mb_internal_encoding('UTF-8');
    }

    protected function loadConfigurationFiles(App $app, $config)
    {
        $files = $this->getConfigurationFiles($app);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }
       
        foreach ($files as $key => $path) {
            $config->set($key, require $path);
        }
    }

    protected function getConfigurationFiles(App $app)
    {
        $files = [];

        //返回规范化的绝对路径名
        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * 获取配置文件嵌套路径
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }
}