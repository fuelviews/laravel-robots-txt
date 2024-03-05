<?php

namespace Fuelviews\RobotsTxt;

use Fuelviews\RobotsTxt\Http\Controllers\RobotsTxtController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Fuelviews\RobotsTxt\Commands\RobotsTxtClearCommand;
use Fuelviews\RobotsTxt\Services\RobotsTxtService;

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
     *
     * @param  Package  $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-robots-txt')
            ->hasConfigFile('robots-txt')
            ->publishesServiceProvider('RobotsTxtServiceProvider')
            ->hasCommand(RobotsTxtClearCommand::class);
    }

    /**
     * Bootstrap any application services.
     *
     * This method performs bootstrapping tasks when the package is booted.
     *
     * @throws BindingResolutionException
     */
    public function bootingPackage(): void
    {
        $robotsTxtService = $this->app->make(RobotsTxtService::class);
        $robotsTxtService->deletePublicRobotsTxt();
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
