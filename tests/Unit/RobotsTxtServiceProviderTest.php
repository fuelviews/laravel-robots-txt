<?php

namespace Fuelviews\RobotsTxt\Tests\Unit;

use Fuelviews\RobotsTxt\Commands\GenerateRobotsTxtCommand;
use Fuelviews\RobotsTxt\Facades\RobotsTxt as RobotsTxtFacade;
use Fuelviews\RobotsTxt\RobotsTxt;
use Fuelviews\RobotsTxt\RobotsTxtServiceProvider;
use Fuelviews\RobotsTxt\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class RobotsTxtServiceProviderTest
 *
 * This class contains unit tests for the RobotsTxtServiceProvider.
 */
class RobotsTxtServiceProviderTest extends TestCase
{
    #[Test]
    public function service_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            RobotsTxtServiceProvider::class,
            $this->app->getProvider(RobotsTxtServiceProvider::class)
        );
    }

    #[Test]
    public function robots_txt_class_is_bound_in_container(): void
    {
        $this->assertTrue($this->app->bound(RobotsTxt::class));

        $instance = $this->app->make(RobotsTxt::class);
        $this->assertInstanceOf(RobotsTxt::class, $instance);
    }

    #[Test]
    public function facade_is_registered(): void
    {
        $this->assertTrue(class_exists(RobotsTxtFacade::class));

        // Test that facade can resolve the underlying class
        $content = RobotsTxtFacade::generate();
        $this->assertIsString($content);
    }

    #[Test]
    public function command_is_registered(): void
    {
        $commands = $this->app['Illuminate\Contracts\Console\Kernel']->all();

        $this->assertArrayHasKey('robots-txt:generate', $commands);
        $this->assertInstanceOf(GenerateRobotsTxtCommand::class, $commands['robots-txt:generate']);
    }

    #[Test]
    public function robots_txt_route_is_registered(): void
    {
        $routes = Route::getRoutes();
        $robotsRoute = $routes->getByName('robots');

        $this->assertNotNull($robotsRoute);
        $this->assertEquals('robots.txt', $robotsRoute->uri());
        $this->assertContains('GET', $robotsRoute->methods());
    }

    #[Test]
    public function config_is_published(): void
    {
        $configPath = config_path('robots-txt.php');

        // The config should be publishable
        $this->artisan('vendor:publish', [
            '--tag' => 'robots-txt-config',
            '--force' => true,
        ])->assertSuccessful();

        $this->assertFileExists($configPath);
    }

    #[Test]
    public function it_writes_the_static_robots_file_after_boot(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.com');
        $path = public_path('robots.txt');
        @unlink($path);

        $provider = new RobotsTxtServiceProvider($this->app);
        $provider->bootingPackage();
        $this->app->boot();

        $this->assertFileExists($path);
        $this->assertStringContainsString('Sitemap: https://example.com/sitemap.xml', file_get_contents($path));

        @unlink($path);
    }

    #[Test]
    public function it_rewrites_a_stale_static_file_when_the_app_url_changes(): void
    {
        Config::set('app.env', 'production');
        $path = public_path('robots.txt');
        $robots = $this->app->make(RobotsTxt::class);

        Config::set('app.url', 'https://old.example.com');
        $robots->syncStaticFile();
        Config::set('app.url', 'https://new.example.com');
        $robots->syncStaticFile();

        $this->assertStringContainsString('Sitemap: https://new.example.com/sitemap.xml', file_get_contents($path));
        $this->assertStringNotContainsString('old.example.com', file_get_contents($path));

        @unlink($path);
    }

    #[Test]
    public function it_does_not_write_the_static_file_when_disabled(): void
    {
        Config::set('robots-txt.static_file', false);
        $path = public_path('robots.txt');
        @unlink($path);

        $provider = new RobotsTxtServiceProvider($this->app);
        $provider->bootingPackage();
        $this->app->boot();

        $this->assertFileDoesNotExist($path);
    }

    #[Test]
    public function it_never_throws_when_the_public_directory_is_missing(): void
    {
        $this->app->make(RobotsTxt::class)->syncStaticFile('/nonexistent-dir-for-robots-test/robots.txt');

        $this->assertFileDoesNotExist('/nonexistent-dir-for-robots-test/robots.txt');
    }
}
