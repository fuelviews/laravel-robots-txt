<?php

namespace Fuelviews\RobotsTxt\Tests\Feature;

use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class GenerateRobotsTxtCommandTest
 *
 * This class contains feature tests for the GenerateRobotsTxtCommand.
 */
class GenerateRobotsTxtCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Cache::flush();
    }

    #[Test]
    public function it_generates_robots_txt_with_default_options(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.com');

        $result = Artisan::call('robots-txt:generate', ['--no-interaction' => true]);

        $this->assertEquals(0, $result);
    }

    #[Test]
    public function it_clears_cache_when_requested(): void
    {
        Cache::forever('robots-txt.checksum', 'test-checksum');

        $this->assertTrue(Cache::has('robots-txt.checksum'));

        Artisan::call('robots-txt:generate', [
            '--clear-cache' => true,
            '--no-interaction' => true,
        ]);

        $this->assertFalse(Cache::has('robots-txt.checksum'));
    }

    #[Test]
    public function it_saves_to_custom_disk_and_path(): void
    {
        Storage::fake('custom');

        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.com');

        Artisan::call('robots-txt:generate', [
            '--disk' => 'custom',
            '--path' => 'custom-robots.txt',
            '--no-interaction' => true,
        ]);

        Storage::disk('custom')->assertExists('custom-robots.txt');
    }

    #[Test]
    public function it_generates_restrictive_content_for_non_production(): void
    {
        Config::set('app.env', 'local');
        Storage::fake('public');

        Artisan::call('robots-txt:generate', [
            '--disk' => 'public',
            '--path' => 'robots.txt',
            '--no-interaction' => true,
        ]);

        $content = Storage::disk('public')->get('robots.txt');

        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /', $content);
    }

    #[Test]
    public function it_handles_command_with_force_option(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('robots.txt', 'existing content');

        Config::set('app.env', 'production');

        Artisan::call('robots-txt:generate', [
            '--disk' => 'public',
            '--path' => 'robots.txt',
            '--force' => true,
            '--no-interaction' => true,
        ]);

        $this->assertTrue(Storage::disk('public')->exists('robots.txt'));
        $content = Storage::disk('public')->get('robots.txt');
        $this->assertNotEquals('existing content', $content);
    }
}
