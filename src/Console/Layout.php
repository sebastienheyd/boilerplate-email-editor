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
    protected $signature = 'email:layout {name : Name of the layout to create} {--remove : Remove the layout file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new layout for boilerplate-email-editor';

    protected $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('email-layouts');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->option('remove') ? $this->remove() : $this->create();
    }

    private function remove()
    {
        $name = Str::snake($this->argument('name')).'.blade.php';

        if (!$this->storage->exists($name)) {
            return $this->error('Layout '.$this->storage->path($name).' does not exists');
        }

        if ($this->confirm('Delete '.$this->storage->path($name).' layout?')) {
            $this->storage->delete($name);
            return $this->info('Layout '.$this->storage->path($name).' has been deleted');
        }
    }

    private function create()
    {
        $label = mb_convert_case($this->argument('name'), MB_CASE_TITLE);
        $name = Str::snake($this->argument('name')).'.blade.php';

        if ($this->storage->exists($name)) {
            return $this->error('Layout '.$this->storage->path($name).' already exist');
        }

        $content = "{{-- $label --}}".PHP_EOL;
        $content .= file_get_contents(__DIR__.'/../resources/views/layout/default.blade.php');
        $this->storage->put($name, $content);

        return $this->info('Layout '.$this->storage->path($name).' has been generated');
    }
}
