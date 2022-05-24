<?php

namespace Rubik\LaravelInvite\Commands;

use Illuminate\Console\Command;

class LaravelInviteCommand extends Command
{
    public $signature = 'laravel-invite';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
