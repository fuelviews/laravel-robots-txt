<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Services\RobotsTxtService;
use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\File;

/**
 * Class DeletePublicRobotsTxtTest
 *
 * This class contains feature tests for deleting the public robots.txt file.
 * It extends the base test case, TestCase.
 */
class DeletePublicRobotsTxtTest extends TestCase
{
    /**
     * Test if the public robots.txt file is deleted successfully.
     *
     * @test
     */
    public function testDeletePublicRobotsTxt()
    {
        $robotsFilePath = public_path('robots.txt');
        File::put($robotsFilePath, "User-agent: *\nDisallow: /");

        $this->assertTrue(File::exists($robotsFilePath));

        $robotsService = new RobotsTxtService();
        $robotsService->deletePublicRobotsTxt();

        $this->assertFalse(File::exists($robotsFilePath));
    }
}
