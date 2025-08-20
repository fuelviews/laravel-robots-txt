<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Facades\RobotsTxt as RobotsTxtFacade;
use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class RobotsTxtFacadeTest
 *
 * This class contains feature tests for the RobotsTxt facade.
 */
class RobotsTxtFacadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    #[Test]
    public function facade_can_get_content(): void
    {
        Config::set('app.env', 'local');

        $content = RobotsTxtFacade::getContent();

        $this->assertIsString($content);
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /', $content);
    }

    #[Test]
    public function facade_can_generate_content(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.com');
        Config::set('robots-txt.user_agents', [
            '*' => [
                'Allow' => ['/'],
                'Disallow' => ['/admin'],
            ],
        ]);

        $content = RobotsTxtFacade::generate();

        $this->assertIsString($content);
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
    }

    #[Test]
    public function facade_can_save_to_file(): void
    {
        Storage::fake('custom');

        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.com');

        RobotsTxtFacade::saveToFile('custom', 'test-robots.txt');

        Storage::disk('custom')->assertExists('test-robots.txt');
    }

    #[Test]
    public function facade_methods_work_with_different_configurations(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.url', 'https://test-site.com');
        Config::set('robots-txt.user_agents', [
            'Googlebot' => [
                'Allow' => ['/api'],
                'Disallow' => ['/private'],
            ],
        ]);
        Config::set('robots-txt.sitemap', ['sitemap.xml']);

        $content = RobotsTxtFacade::generate();

        $this->assertStringContainsString('User-agent: Googlebot', $content);
        $this->assertStringContainsString('Allow: /api', $content);
        $this->assertStringContainsString('Disallow: /private', $content);
        $this->assertStringContainsString('Sitemap: https://test-site.com/sitemap.xml', $content);
    }
}
