<?php

namespace Rubik\LaravelInvite;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Rubik\LaravelInvite\Commands\LaravelInviteCommand;

class LaravelInviteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-invite')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-invite_table')
            ->hasCommand(LaravelInviteCommand::class);
    }
}
