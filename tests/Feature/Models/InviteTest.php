<?php

use Rubik\LaravelInvite\Models\Invite;

it('can create an Invite', function () {
    Invite::factory()->expired()->create();
    Invite::factory()->accepted()->create();
    Invite::factory()->declined()->create();
    Invite::factory()->notExpired()->create();

    expect(Invite::expired()->count())->toBe(1);
});
