<?php

use Illuminate\Support\Collection;
use Rubik\LaravelInvite\Enums\InviteeState;
use Rubik\LaravelInvite\Models\Invitation;
use Rubik\LaravelInvite\Tests\TestSupport\Models\TestModelInvitee;
use function PHPUnit\Framework\assertInstanceOf;

beforeEach(function () {
    $this->testModel = createTestModel(TestModelInvitee::class);
});

it('has invitations', function () {
    Invitation::factory()->for($this->testModel, 'invitable')->create();

    assertInstanceOf(Collection::class, $this->testModel->invitations);
    expect($this->testModel->invitations->count())->toBe(1);
});

it('can determine whether an invitee has specific type of invites', function ($state, $function, $otherStates) {

    foreach ($otherStates as $otherState) {
        Invitation::factory()->for($this->testModel, 'invitable')->$otherState()->create();
    }

    expect($this->testModel->$function())->toBeFalse();

    Invitation::factory()->for($this->testModel, 'invitable')->$state()->create();

    expect($this->testModel->$function())->toBeTrue();
})->with([
    'pending' => ['pending', 'hasPendingInvitations', ['expired', 'accepted', 'declined']],
    'expired' => ['expired', 'hasExpiredInvitations', ['accepted', 'declined', 'pending']],
    'accepted' => ['accepted', 'hasAcceptedInvitations', ['expired', 'declined', 'pending']],
    'declined' => ['declined', 'hasDeclinedInvitations', ['expired', 'accepted', 'pending']],
]);

it('can return the state of an invitee', function ($state, $otherStates) {

    foreach ($otherStates as $otherState) {
        Invitation::factory()->for($this->testModel, 'invitable')->$otherState()->create();
    }

    expect($this->testModel->state)->toBe($state);

})->with([
    'pending' => [InviteeState::PENDING, ['expired', 'pending', 'expired']],
    'expired' => [InviteeState::EXPIRED, ['expired', 'expired']],
    'accepted' => [InviteeState::ACCEPTED, ['expired', 'declined', 'pending', 'accepted', 'declined', 'pending', 'expired']],
    'declined' => [InviteeState::DECLINED, ['expired', 'pending', 'declined', 'pending', 'expired']],
    'none' => [null, []],
]);
