<?php

namespace CodeIQ B.V.\LaravelMww\Commands;

use Illuminate\Console\Command;

class LaravelMwwCommand extends Command
{
    public $signature = 'laravel-mww';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
