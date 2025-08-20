<?php

namespace Fuelviews\RobotsTxt\Tests\Unit;

use Fuelviews\RobotsTxt\Commands\GenerateRobotsTxtCommand;
use Fuelviews\RobotsTxt\Facades\RobotsTxt as RobotsTxtFacade;
use Fuelviews\RobotsTxt\RobotsTxt;
use Fuelviews\RobotsTxt\RobotsTxtServiceProvider;
use Fuelviews\RobotsTxt\Tests\TestCase;
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
    public function static_robots_file_removal_works(): void
    {
        // Create a mock static robots.txt file
        $staticRobotsPath = public_path('robots.txt');
        $directory = dirname($staticRobotsPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($staticRobotsPath, 'Static robots.txt content');
        $this->assertFileExists($staticRobotsPath);

        // Re-boot the service provider to trigger static file removal
        $provider = new RobotsTxtServiceProvider($this->app);
        $provider->bootingPackage();

        $this->assertFileDoesNotExist($staticRobotsPath);
    }
}
