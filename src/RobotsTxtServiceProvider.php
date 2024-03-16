<?php

namespace Fuelviews\RobotsTxt;

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
            ->name('laravel-robots-txt')
            ->hasConfigFile('robots-txt');
    }

    /**
     * Bootstrap any application services.
     *
     * This method performs bootstrapping tasks when the package is booted.
     */
    public function bootingPackage(): void
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Register package routes.
     *
     * This method registers the route for serving the robots.txt file.
     */
    public function PackageRegistered(): void
    {
        Route::get('robots.txt', RobotsTxtController::class);
    }
}
