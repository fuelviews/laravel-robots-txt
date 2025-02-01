<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\RobotsTxt;
use Fuelviews\RobotsTxt\Tests\TestCase;

/**
 * Class RobotsTxtSitemapTest
 *
 * This class contains feature tests for the sitemap functionality in the RobotsTxt class.
 * It extends the base test case, TestCase.
 */
class RobotsTxtSitemapTest extends TestCase
{
    /**
     * Test if a singular sitemap is included in the generated robots.txt content.
     *
     * @test
     */
    public function it_includes_a_singular_sitemap_in_robots_txt(): void
    {
        $robotTxtGenerator = new RobotsTxt();
        $robotsContent = $robotTxtGenerator->generate();

        $baseUrl = config('app.url');

        $this->assertStringContainsString('Sitemap: '.$baseUrl.'/'.'sitemap.xml', $robotsContent);
    }

    /**
     * Test if multiple sitemaps are included in the generated robots.txt content.
     *
     * @test
     */
    public function it_includes_multiple_sitemaps_in_robots_txt(): void
    {
        $this->setTestConfigurations([
            'robots-txt.sitemap' => [
                'sitemap.xml',
                'sitemap_pages.xml',
                'sitemap_posts.xml',
            ],
        ]);

        $robotTxtGenerator = new RobotsTxt();
        $robotsContent = $robotTxtGenerator->generate();

        $baseUrl = config('app.url');

        $this->assertStringContainsString('Sitemap: '.$baseUrl.'/'.'sitemap.xml', $robotsContent);
        $this->assertStringContainsString('Sitemap: '.$baseUrl.'/'.'sitemap_pages.xml', $robotsContent);
        $this->assertStringContainsString('Sitemap: '.$baseUrl.'/'.'sitemap_posts.xml', $robotsContent);
    }
}
