<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Tests;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

trait TestHelper
{
    /**
     * Create a modified copy of testbench to be used as a template.
     * Before each test, a fresh copy of the template is created.
     */
    private static function setUpLocalTestbench()
    {
        if (! file_exists(self::TEST_APP_TEMPLATE)) {
            fwrite(STDOUT, 'Setting up test environment for first use.'.PHP_EOL);
            $files = new Filesystem();
            $files->makeDirectory(self::TEST_APP_TEMPLATE, 0755, true);
            $original = __DIR__.'/../vendor/orchestra/testbench-core/laravel/';
            $files->copyDirectory($original, self::TEST_APP_TEMPLATE);

            // Modify the composer.json file
            $composer = json_decode($files->get(self::TEST_APP_TEMPLATE.'/composer.json'), true);

            // Remove "tests/TestCase.php" from autoload (it doesn't exist)
            unset($composer['autoload']['classmap'][1]);

            // Pre-install illuminate/support
            $composer['require'] = [
                'laravel/framework' => '^7.0|^8.0',
                'laravel/sanctum' => '^7.0|^8.0',
            ];
            $composer['require-dev'] = new \StdClass();

            // Install stable version
            $composer['minimum-stability'] = 'dev';
            $composer['prefer-stable'] = true;
            $files->put(self::TEST_APP_TEMPLATE.'/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Install dependencies
            fwrite(STDOUT, "Installing test environment dependencies\n");
            (new Process(['composer', 'install', '-q'], self::TEST_APP_TEMPLATE))->run(function ($type, $buffer) {
                fwrite(STDOUT, $buffer);
            });
        }

        (new Filesystem())->copyDirectory(self::TEST_APP_TEMPLATE, self::TEST_APP);
    }

    private static function removeTestbench()
    {
        $files = new Filesystem();
        if ($files->exists(self::TEST_APP)) {
            $files->deleteDirectory(self::TEST_APP);
        }
        if ($files->exists(self::TEST_APP_TEMPLATE)) {
            $files->deleteDirectory(self::TEST_APP_TEMPLATE);
        }
    }

    /**
     * @return bool
     */
    protected function runProcess(array $command)
    {
        $process = new Process($command, self::TEST_APP);
        $process->run();

        return $process->getExitCode() === 0;
    }

    protected function installTestApp()
    {
        $this->uninstallTestApp();
        (new Filesystem())->copyDirectory(self::TEST_APP_TEMPLATE, self::TEST_APP);
    }

    protected function uninstallTestApp()
    {
        $files = new Filesystem();
        if ($files->exists(self::TEST_APP)) {
            $files->deleteDirectory(self::TEST_APP);
        }
    }
}
