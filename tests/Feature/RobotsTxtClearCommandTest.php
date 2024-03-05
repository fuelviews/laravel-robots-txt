<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

/**
 * Class RobotsTxtClearCommandTest
 *
 * This class contains feature tests for clearing the robots.txt cache using the command.
 * It extends the base test case, TestCase.
 */
class RobotsTxtClearCommandTest extends TestCase
{
    /**
     * Test if the robots.txt cache is cleared successfully.
     *
     * @test
     */
    public function it_clears_the_robots_txt_cache()
    {
        $cacheKey = 'robots-txt.checksum';
        Cache::forever($cacheKey, 'dummy_checksum_value');

        $this->artisan('robots-txt:clear')
            ->assertExitCode(0);

        $this->assertFalse(Cache::has($cacheKey));
    }
}
