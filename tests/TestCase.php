<?php

namespace Fuelviews\RobotsTxt\Tests;

use Fuelviews\RobotsTxt\RobotsTxtServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 *
 * This class is the base test case for the laravel-robots-txt package.
 * It extends the Orchestra\Testbench\TestCase class.
 */
class TestCase extends Orchestra
{
    /**
     * Set up the test environment.
     *
     * This method is called before each test method is executed.
     */
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpStorage();
    }

    /**
     * Get the service providers for the test environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            RobotsTxtServiceProvider::class,
        ];
    }

    /**
     * Set up the environment configuration for the test application.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->get('app.url', 'https://example.com/');
        $app['config']->set('app.env', 'production');
        $app['config']->get('robots-txt.deny_development_url', 'development.');
        $app['config']->get('robots-txt.sitemap', [
            'sitemap.xml',
        ]);
    }

    /**
     * Set up fake storage for tests.
     */
    protected function setUpStorage(): void
    {
        Storage::fake('public');
    }

    /**
     * Helper method to set application configurations for testing.
     */
    protected function setTestConfigurations(array $configurations): void
    {
        foreach ($configurations as $key => $value) {
            Config::set($key, $value);
        }
    }
}
