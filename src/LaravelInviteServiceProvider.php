<?php

namespace Rubik\LaravelInvite;

use Illuminate\Console\Scheduling\Schedule;
use Rubik\LaravelInvite\Commands\DeleteExpiredInvitesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelInviteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-invite')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_invites_table')
            ->hasCommand(DeleteExpiredInvitesCommand::class);
    }

    public function packageBooted()
    {
        $this->app->booted(function () {
            if (config('invite.expire.delete.auto')) {
                $schedule = app(Schedule::class);
                $schedule->command('invite:delete-expired')->hourly();
            }
        });
    }

    public function registeringPackage()
    {
        $this->app->singleton('laravel-invite', function () {
            return new Invitation();
        });
    }

    public function provides(): array
    {
        return ['laravel-invite'];
    }
}
