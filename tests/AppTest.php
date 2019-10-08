<?php

use PHPUnit\Framework\TestCase;
use Shea\App;

class AppTest extends TestCase 
{
    /**
     * @test
     */
    public function app()
    {
        $this->assertEquals("0.0.1", (new App())->version());
    }
}