<?php

namespace Fuelviews\RobotsTxt\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Class RobotsTxtClearCommand
 *
 * This class represents a console command for clearing the cache related to robots.txt.
 * It extends the Illuminate\Console\Command class.
 */
class RobotsTxtClearCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    public $signature = 'robots-txt:clear';

    /**
     * The description of the command.
     *
     * @var string
     */
    public $description = 'Clears robots.txt cache';

    /**
     * Handle the console command.
     *
     * This method is invoked when the command is executed.
     */
    public function handle(): int
    {
        $cacheKey = 'robots-txt.checksum';

        Cache::forget($cacheKey);

        $this->info('The cache for robots.txt has been cleared.');

        return self::SUCCESS;
    }
}
