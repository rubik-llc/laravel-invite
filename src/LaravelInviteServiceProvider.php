<?php

namespace Rubik\LaravelInvite;

use Rubik\LaravelInvite\Commands\DeleteExpiredInvitesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommand(DeleteExpiredInvitesCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton('laravel-invite', function () {
            return new Invite();
        });
    }

    public function provides(): array
    {
        return ['laravel-invite'];
    }
}
