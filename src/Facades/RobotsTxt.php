<?php

namespace Fuelviews\RobotsTxt\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class RobotsTxt
 *
 * This class represents a facade for interacting with the RobotsTxt service.
 * It extends the Illuminate\Support\Facades\Facade class.
 */
class RobotsTxt extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Fuelviews\RobotsTxt\RobotsTxt::class;
    }
}
