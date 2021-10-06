<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Tests;

use Orchestra\Testbench\TestCase as TestBench;
use Sebastienheyd\Boilerplate\BoilerplateServiceProvider;
use Sebastienheyd\BoilerplateEmailEditor\ServiceProvider;

abstract class TestCase extends TestBench
{
    use TestHelper;

    protected const TEST_APP_TEMPLATE = __DIR__.'/../testbench/template';

    protected const TEST_APP = __DIR__.'/../testbench/laravel';

    public static function setUpBeforeClass(): void
    {
        self::removeTestbench();
        self::setUpLocalTestbench();
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTestbench();
        parent::tearDownAfterClass();
    }

    protected function getBasePath()
    {
        return self::TEST_APP;
    }

    protected function getPackageProviders($app)
    {
        return [
            BoilerplateServiceProvider::class,
            ServiceProvider::class,
        ];
    }
}
