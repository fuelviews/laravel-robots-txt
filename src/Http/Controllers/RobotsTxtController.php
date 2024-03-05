<?php

namespace Fuelviews\RobotsTxt\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Fuelviews\RobotsTxt\Services\RobotsTxtService;

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
     *
     * @var RobotsTxtService
     */
    protected RobotsTxtService $robotsTxtService;

    /**
     * Create a new RobotsTxtController instance.
     *
     * @param  RobotsTxtService  $robotsTxtService
     */
    public function __construct(RobotsTxtService $robotsTxtService)
    {
        $this->robotsTxtService = $robotsTxtService;
    }

    /**
     * Handle the incoming request.
     *
     * This method is invoked when a request is made to the controller.
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        $contents = $this->robotsTxtService->getContent();

        return response($contents, 200)->header('Content-Type', 'text/plain');
    }
}
