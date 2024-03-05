<?php

namespace Fuelviews\RobotsTxt\Http\Controllers;

use Fuelviews\RobotsTxt\Services\RobotsTxtService;
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
    protected RobotsTxtService $robotsTxtService;

    /**
     * Create a new RobotsTxtController instance.
     */
    public function __construct(RobotsTxtService $robotsTxtService)
    {
        $this->robotsTxtService = $robotsTxtService;
    }

    /**
     * Handle the incoming request.
     *
     * This method is invoked when a request is made to the controller.
     */
    public function __invoke(): Response
    {
        $contents = $this->robotsTxtService->getContent();

        return response($contents, 200)->header('Content-Type', 'text/plain');
    }
}
