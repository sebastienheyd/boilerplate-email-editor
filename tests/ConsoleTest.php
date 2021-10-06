<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Tests;

use Symfony\Component\Console\Exception\RuntimeException;

class ConsoleTest extends TestCase
{
    public function testEmailLayoutNoName()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('email:layout');
    }

    public function testEmailLayoutCreate()
    {
        $this->artisan('email:layout', ['name' => 'test'])
            ->expectsOutput('Layout '.self::TEST_APP.'/resources/views/email-layouts/test.blade.php has been generated')
            ->assertExitCode(0);

        $this->assertFileExists(self::TEST_APP.'/resources/views/email-layouts/test.blade.php');

        $this->artisan('email:layout', ['name' => 'test'])
            ->expectsOutput('Layout '.self::TEST_APP.'/resources/views/email-layouts/test.blade.php already exist')
            ->assertExitCode(0);
    }

    public function testEmailLayoutDelete()
    {
        $this->artisan('email:layout', ['--remove' => true, 'name' => 'test'])
            ->expectsConfirmation('Delete '.self::TEST_APP.'/resources/views/email-layouts/test.blade.php layout?', 'yes')
            ->expectsOutput('Layout '.self::TEST_APP.'/resources/views/email-layouts/test.blade.php has been deleted')
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(self::TEST_APP.'/resources/views/email-layouts/test.blade.php');

        $this->artisan('email:layout', ['--remove' => true, 'name' => 'test'])
            ->expectsOutput('Layout '.self::TEST_APP.'/resources/views/email-layouts/test.blade.php does not exists')
            ->assertExitCode(0);
    }
}
