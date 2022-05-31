<?php

use Carbon\Carbon;
use Illuminate\Console\Command;
use Rubik\LaravelInvite\Commands\DeleteExpiredInvitesCommand;
use Rubik\LaravelInvite\Models\Invite;
use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze();

    Invite::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after') + 1, config('invite.unit')))->count(5)->create();
    Invite::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after') - 1, config('invite.unit')))->count(10)->create();
    Invite::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after'), config('invite.unit')))->count(6)->create();

});

it('can delete expired invites', function () {

    expect(Invite::count())->toBe(21);

    artisan(DeleteExpiredInvitesCommand::class)->assertExitCode(Command::SUCCESS);

    expect(Invite::count())->toBe(10);

});

it('can delete all expired invites', function () {

    expect(Invite::count())->toBe(21);

    artisan('laravel-invite:delete-expired --all')->assertExitCode(Command::SUCCESS);

    expect(Invite::count())->toBe(0);

});

it('reports progress', function () {

    artisan(DeleteExpiredInvitesCommand::class)
        ->expectsOutput('Deleting invites...')
        ->expectsOutput('Deleted 11 invites!')
        ->assertExitCode(Command::SUCCESS);

});
