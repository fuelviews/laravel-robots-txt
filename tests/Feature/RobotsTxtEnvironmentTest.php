<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Tests\TestCase;

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
     *
     * @test
     */
    public function it_disallows_in_local_environment()
    {
        $this->setTestConfigurations([
            'app.env' => 'local',
        ]);

        $response = $this->get('robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /');
    }

    /**
     * Test if all paths are disallowed in non-production environments.
     *
     * @test
     */
    public function it_disallows_in_development_environment()
    {
        $this->setTestConfigurations([
            'app.env' => 'development',
        ]);

        $response = $this->get('robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /');
    }

    /**
     * Test if the robots.txt is generated correctly in the production environment.
     *
     * @test
     */
    public function it_allows_in_production_environment()
    {
        $this->setTestConfigurations([
            'app.env' => 'production',
        ]);

        $baseUrl = config('app.url');

        $response = $this->get('robots.txt');
        $response->assertStatus(200);

        $userAgentsRules = config('robots-txt.user_agents');
        foreach ($userAgentsRules as $userAgent => $rules) {
            foreach ($rules as $ruleType => $paths) {
                foreach ($paths as $path) {
                    $response->assertSee("User-agent: $userAgent");
                    $response->assertSee("$ruleType: $path");
                }
            }
        }

        $sitemaps = config('robots-txt.sitemap');
        foreach ($sitemaps as $sitemapPath) {
            $response->assertSee('Sitemap: '.($baseUrl).'/'.$sitemapPath);
        }
    }
}
