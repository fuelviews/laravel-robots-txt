<?php

namespace Fuelviews\RobotsTxt;

use Fuelviews\RobotsTxt\Commands\GenerateRobotsTxtCommand;
use Fuelviews\RobotsTxt\Http\Controllers\RobotsTxtController;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Class RobotsTxtServiceProvider
 *
 * This class is the service provider for the laravel-robots-txt package.
 * It configures the package, registers routes, and performs bootstrapping tasks.
 */
class RobotsTxtServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('robots-txt')
            ->hasConfigFile('robots-txt')
            ->hasCommand(GenerateRobotsTxtCommand::class);
    }

    /**
     * Register package services.
     */
    public function registeringPackage(): void
    {
        $this->app->singleton(RobotsTxt::class, function ($app) {
            return new RobotsTxt();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * This method performs bootstrapping tasks when the package is booted.
     * It removes any existing static robots.txt file to prevent conflicts.
     */
    public function bootingPackage(): void
    {
        $this->removeStaticRobotsFile();
    }

    /**
     * Remove static robots.txt file if it exists.
     *
     * This prevents conflicts with our dynamic route-based robots.txt.
     */
    protected function removeStaticRobotsFile(): void
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            if (@unlink($path)) {
                $this->app['log']?->info('Static robots.txt file removed to prevent conflicts with dynamic generation.');
            }
        }
    }

    /**
     * Register package routes.
     *
     * This method registers the route for serving the robots.txt file.
     */
    public function packageRegistered(): void
    {
        Route::get('robots.txt', RobotsTxtController::class)->name('robots');
    }
}
