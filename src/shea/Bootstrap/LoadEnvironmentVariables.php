<?php

namespace Shea\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Shea\App;

class LoadEnvironmentVariables
{
    public function bootstrap(App $app)
    {
        try {
            $this->createDotenv($app)->safeLoad();
        } catch (InvalidFileException $e) {
            echo 'The environment file is invalid: '.$e->getMessage();
            die(1);
        }
    }

    protected function createDotenv($app)
    {
        return Dotenv::create(
            $app->environmentPath(),
            $app->environmentFile()
        );
    }
}