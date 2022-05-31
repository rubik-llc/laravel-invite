<?php

use Carbon\Carbon;
use Illuminate\Console\Command;
use Rubik\LaravelInvite\Commands\DeleteExpiredInvitesCommand;
use Rubik\LaravelInvite\Models\Invitation;
use function Pest\Laravel\artisan;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze();

    Invitation::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after') + 1, config('invite.unit')))->count(5)->create();
    Invitation::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after') - 1, config('invite.unit')))->count(10)->create();
    Invitation::factory()->expiresAt(Carbon::now()->sub(config('invite.expire.after'), config('invite.unit')))->count(6)->create();

});

it('can delete expired invites', function () {

    expect(Invitation::count())->toBe(21);

    artisan(DeleteExpiredInvitesCommand::class)->assertExitCode(Command::SUCCESS);

    expect(Invitation::count())->toBe(10);

});

it('can delete all expired invites', function () {

    expect(Invitation::count())->toBe(21);

    artisan('invite:delete-expired --all')->assertExitCode(Command::SUCCESS);

    expect(Invitation::count())->toBe(0);

});

it('reports progress', function () {

    artisan(DeleteExpiredInvitesCommand::class)
        ->expectsOutput('Deleting invitations...')
        ->expectsOutput('Deleted 11 invitations!')
        ->assertExitCode(Command::SUCCESS);

});
