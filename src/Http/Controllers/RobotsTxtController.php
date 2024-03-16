<?php

namespace Fuelviews\RobotsTxt\Http\Controllers;

use Fuelviews\RobotsTxt\RobotsTxt;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class RobotsTxtController
 *
 * This class represents a controller for handling requests related to robots.txt.
 * It extends the Illuminate\Routing\Controller class.
 */
class RobotsTxtController extends BaseController
{
    /**
     * The RobotsTxtService instance.
     */
    protected RobotsTxt $robotsTxt;

    /**
     * Create a new RobotsTxtController instance.
     */
    public function __construct(RobotsTxt $robotsTxt)
    {
        $this->robotsTxt = $robotsTxt;
    }

    /**
     * Handle the incoming request.
     *
     * This method is invoked when a request is made to the controller.
     */
    public function __invoke(): Response
    {
        $contents = $this->robotsTxt->getContent();

        return response($contents, '200')->header('Content-Type', 'text/plain');
    }
}
