<?php

namespace Fuelviews\RobotsTxt\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class RobotsTxt
 *
 * This class provides a facade for the RobotsTxt package functionality.
 * It allows easy access to robots.txt generation and management methods.
 *
 * @method static string getContent()
 * @method static string generate()
 * @method static void saveToFile(string $disk, string $path)
 *
 * @see \Fuelviews\RobotsTxt\RobotsTxt
 */
class RobotsTxt extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Fuelviews\RobotsTxt\RobotsTxt::class;
    }
}
