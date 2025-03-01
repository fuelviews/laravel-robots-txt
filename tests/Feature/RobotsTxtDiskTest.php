<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\RobotsTxt;
use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Class RobotsTxtDiskTest
 *
 * This class contains feature tests for saving the robots.txt file to a specified disk.
 * It extends the base test case, TestCase.
 */
class RobotsTxtDiskTest extends TestCase
{
    /**
     * @test
     */
    public function it_saves_robots_txt_to_specified_disk(): void
    {
        $robotTxtService = new RobotsTxt();
        $robotTxtService->saveToFile('public', '/robots-txt/robots.txt');

        Storage::disk('public')->assertExists('/robots-txt/robots.txt');
    }
}
