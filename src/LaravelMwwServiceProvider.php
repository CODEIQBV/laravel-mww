<?php

namespace CodeIQ B.V.\LaravelMww;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CodeIQ B.V.\LaravelMww\Commands\LaravelMwwCommand;

class LaravelMwwServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-mww')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_mww_table')
            ->hasCommand(LaravelMwwCommand::class);
    }
}
