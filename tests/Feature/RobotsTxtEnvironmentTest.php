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
     * Test if the robots.txt is generated correctly in the production environment.
     *
     * @test
     */
    public function it_generates_robots_txt_in_production_environment()
    {
        $this->setTestConfigurations([
            'robots-txt.user_agents' => [
                '*' => [
                    'Allow' => [
                        '/'
                    ],
                    'Disallow' => [
                        '/admin',
                        '/dashboard',
                    ],
                ],
            ],
            'sitemap' => [
                'sitemap.xml',
            ],
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
            $response->assertSee('Sitemap: ' . ($baseUrl) . $sitemapPath);
        }
    }

    /**
     * Data provider for environments.
     *
     * @return array
     */
    public static function environmentProvider(): array
    {
        return [
            ['local'],
            ['development'],
        ];
    }

    /**
     * Test if all paths are disallowed in non-production environments.
     *
     * @test
     * @dataProvider environmentProvider
     */
    public function it_disallows_all_in_non_production_environment($env)
    {
        $this->setTestConfigurations([
            'app.env' => $env,
        ]);

        $response = $this->get('robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /');
    }

    /**
     * Test if all paths are disallowed in production environment when URL matches deny pattern.
     *
     * @test
     */
    public function it_disallows_all_if_url_matches_deny_pattern_in_production_environment()
    {
        $baseUrl = config('app.url');
        $parsedUrl = parse_url($baseUrl);
        $developmentDomain = 'robots-txt.deny_url_pattern' . $parsedUrl['host'];

        $this->setTestConfigurations([
            'app.url' => $developmentDomain,
        ]);

        $response = $this->get('robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /');
    }
}
