<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Layout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:layout {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new layout for boilerplate-email-editor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $label = mb_convert_case($this->argument('name'), MB_CASE_TITLE);
        $name = Str::snake($this->argument('name')) . '.blade.php';
        $storage = Storage::disk('email-layouts');

        if ($storage->exists($name)) {
            return $this->error('Layout ' . $storage->path($name) . ' already exist');
        }

        $content = "{{-- $label --}}" . PHP_EOL;
        $content .= file_get_contents(__DIR__ . '/../resources/views/layout/default.blade.php');
        $storage->put($name, $content);

        return $this->info('Layout ' . $storage->path($name) . ' has been generated');
    }
}
