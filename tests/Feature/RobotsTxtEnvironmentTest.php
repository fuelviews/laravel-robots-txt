<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class RobotsTxtEnvironmentTest
 *
 * This class contains feature tests for generating the robots.txt file based on environment configurations.
 * It extends the base test case, TestCase.
 */
class RobotsTxtEnvironmentTest extends TestCase
{
    /**
     * Test if all paths are disallowed in non-production environments.
     */
    #[Test]
    public function it_disallows_in_local_environment(): void
    {
        $this->setTestConfigurations([
            'app.env' => 'local',
        ]);

        $testResponse = $this->get('robots.txt');

        $testResponse->assertStatus(200);
        $testResponse->assertSee('User-agent: *');
        $testResponse->assertSee('Disallow: /');
    }

    /**
     * Test if all paths are disallowed in non-production environments.
     */
    #[Test]
    public function it_disallows_in_development_environment(): void
    {
        $this->setTestConfigurations([
            'app.env' => 'development',
        ]);

        $testResponse = $this->get('robots.txt');

        $testResponse->assertStatus(200);
        $testResponse->assertSee('User-agent: *');
        $testResponse->assertSee('Disallow: /');
    }

    /**
     * Test if the robots.txt is generated correctly in the production environment.
     */
    #[Test]
    public function it_allows_in_production_environment(): void
    {
        $this->setTestConfigurations([
            'app.env' => 'production',
        ]);

        $baseUrl = config('app.url');

        $testResponse = $this->get('robots.txt');
        $testResponse->assertStatus(200);

        $userAgentsRules = config('robots-txt.user_agents');
        foreach ($userAgentsRules as $userAgent => $rules) {
            foreach ($rules as $ruleType => $paths) {
                foreach ($paths as $path) {
                    $testResponse->assertSee('User-agent: '.$userAgent);
                    $testResponse->assertSee(sprintf('%s: %s', $ruleType, $path));
                }
            }
        }

        $sitemaps = config('robots-txt.sitemap');
        foreach ($sitemaps as $sitemap) {
            $testResponse->assertSee('Sitemap: '.($baseUrl).'/'.$sitemap);
        }
    }
}
