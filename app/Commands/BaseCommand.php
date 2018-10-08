<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    protected $commands = [
        'ip' => '/bin/ip',
        'grep' => '/bin/grep'
    ];

    public function amIRoot() 
    {
        return (posix_getuid() === 0);
    }

    public function getCommand($cmd) 
    {
        if(isset($this->commands[$cmd])) {
            return $this->commands[$cmd];
        }

        return $cmd;
    }

}
