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
     * Writes public/robots.txt once the application has booted, so config
     * overrides applied by other providers during boot are included.
     */
    public function bootingPackage(): void
    {
        if (! config('robots-txt.static_file', true)) {
            return;
        }

        $this->app->booted(function (): void {
            $this->app->make(RobotsTxt::class)->syncStaticFile();
        });
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
