<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Rubik\LaravelInvite\Events\InvitationCreated;
use Rubik\LaravelInvite\Exceptions\EmailNotProvidedException;
use Rubik\LaravelInvite\Exceptions\EmailNotValidException;
use Rubik\LaravelInvite\Facades\Invitation;
use Rubik\LaravelInvite\Tests\TestSupport\Models\TestModel;
use Rubik\LaravelInvite\Tests\TestSupport\Models\User;
use function Spatie\PestPluginTestTime\testTime;

it('can return specific type of invites', function ($data, $value) {
    Invitation::factory()->pending()->create();
    Invitation::factory()->pending()->create();
    Invitation::factory()->expired()->create();
    Invitation::factory()->accepted()->create();
    Invitation::factory()->declined()->create();
    Invitation::factory()->declined()->create();
    Invitation::factory()->declined()->create();

    expect(Invitation::$data()->count())->toBe($value);
    expect(Invitation::count())->toBe(7);
})->with(
    [
        'pending invites' => ['pending', 2],
        'expired invites' => ['expired', 1],
        'accepted invites' => ['accepted', 1],
        'declined invites' => ['declined', 3],
    ]
);

it('can retrieve an invite by its token', function () {
    $invite = Invitation::factory()->create();

    expect(Invitation::withToken($invite->token)->token)->toBe($invite->token);
    expect(Invitation::withToken($invite->token))->not()->toBeNull();
});


it('can determine if an invite is pending', function ($data, $value) {
    expect($data->isPending())->toBe($value);
})->with(
    [
        'invite is pending' => [fn () => Invitation::factory()->pending()->create(), true],
        'invite is expired' => [fn () => Invitation::factory()->expired()->create(), false],
        'invite is accepted' => [fn () => Invitation::factory()->accepted()->create(), false],
        'invite is declined' => [fn () => Invitation::factory()->declined()->create(), false],
    ]
);

it('can determine if an invite is expired', function ($data, $value) {
    expect($data->isExpired())->toBe($value);
})->with(
    [
        'invite is pending' => [fn () => Invitation::factory()->pending()->create(), false],
        'invite is expired' => [fn () => Invitation::factory()->expired()->create(), true],
        'invite is accepted' => [fn () => Invitation::factory()->accepted()->create(), false],
        'invite is expired and accepted' => [fn () => Invitation::factory()->expired()->accepted()->create(), false],
        'invite is declined' => [fn () => Invitation::factory()->declined()->create(), false],
        'invite is expired and declined' => [fn () => Invitation::factory()->expired()->declined()->create(), false],

    ]
);

it('can determine if an invite is accepted', function ($data, $value) {
    expect($data->isAccepted())->toBe($value);
})->with(
    [
        'invite is pending' => [fn () => Invitation::factory()->pending()->create(), false],
        'invite is expired' => [fn () => Invitation::factory()->expired()->create(), false],
        'invite is accepted' => [fn () => Invitation::factory()->accepted()->create(), true],
        'invite is expired and accepted' => [fn () => Invitation::factory()->expired()->accepted()->create(), true],
        'invite is declined' => [fn () => Invitation::factory()->declined()->create(), false],
        'invite is expired and declined' => [fn () => Invitation::factory()->expired()->declined()->create(), false],
    ]
);

it('can determine if an invite is declined', function ($data, $value) {
    expect($data->isDeclined())->toBe($value);
})->with(
    [
        'invite is pending' => [fn () => Invitation::factory()->pending()->create(), false],
        'invite is expired' => [fn () => Invitation::factory()->expired()->create(), false],
        'invite is accepted' => [fn () => Invitation::factory()->accepted()->create(), false],
        'invite is expired and accepted' => [fn () => Invitation::factory()->expired()->accepted()->create(), false],
        'invite is declined' => [fn () => Invitation::factory()->declined()->create(), true],
        'invite is expired and declined' => [fn () => Invitation::factory()->expired()->declined()->create(), true],
    ]
);

it('belongs to referable', function (string $model) {
    $invite = Invitation::factory()->for($model::factory(), 'referable')->create();
    $this->assertInstanceOf($model, $invite->referable);
})->with([
    [User::class],
    [TestModel::class],
]);

it('belongs to invitable', function (string $model) {
    $invite = Invitation::factory()->for($model::factory(), 'invitable')->create();
    $this->assertInstanceOf($model, $invite->invitable);
})->with([
    [User::class],
    [TestModel::class],
]);

it('can be accepted', function () {
    testTime()->freeze();

    $invite = Invitation::factory()->pending()->create();
    expect($invite->refresh()->accepted_at)->toBeNull();

    $invite->accept();
    expect($invite->refresh()->accepted_at)->toBe(Carbon::now()->format('Y-m-d H:i:s'));
});

it('can be declined', function () {
    testTime()->freeze();

    $invite = Invitation::factory()->pending()->create();
    expect($invite->refresh()->declined_at)->toBeNull();

    $invite->decline();
    expect($invite->refresh()->declined_at)->toBe(Carbon::now()->format('Y-m-d H:i:s'));
});

it('can make an invite', function () {
    Invitation::to('test@email.com')->make();

    expect(Invitation::count())->toBe(1);
    expect(Invitation::first()->email)->toBe('test@email.com');
});


it('can make an invite with referer', function () {
    $user = User::factory()->create();
    Invitation::to('test@email.com')->referer($user)->make();

    expect(Invitation::count())->toBe(1);
    expect(Invitation::first()->referable_id)->toBe(strval($user->id));
});

it('can make an invite with invitee', function () {
    $user = User::factory()->create();
    Invitation::to('test@email.com')->invitee($user)->make();

    expect(Invitation::count())->toBe(1);
    expect(Invitation::first()->invitable_id)->toBe(strval($user->id));
});

it('can make an invite with specific expiration date', function ($data, $value) {
    testTime()->freeze();

    Invitation::to('test@email.com')->expireAt($data)->make();

    expect(Invitation::count())->toBe(1);
    expect(Invitation::first()->expires_at)->toBe($value);
})->with([
    'carbon instance' => [fn () => Carbon::now(), fn () => Carbon::now()->format('Y-m-d H:i:s')],
    'date as string' => ['2022-02-02', '2022-02-02 00:00:00'],
    'date and time as string' => ['2022-02-02 12:35:07', '2022-02-02 12:35:07'],
]);

it('can make an invite with an expiration date after a specific amount of time', function ($data, $unit, $value) {
    testTime()->freeze();

    Invitation::to('test@email.com')->expireIn($data, $unit)->make();

    expect(Invitation::count())->toBe(1);
    expect(Invitation::first()->expires_at)->toBe($value);
})->with([
    'days' => [3, 'days', fn () => Carbon::now()->addDays(3)->format('Y-m-d H:i:s')],
    'hours' => [2, 'hours', fn () => Carbon::now()->addHours(2)->format('Y-m-d H:i:s')],
]);


it('will fire an event when an invitation is made', function () {
    Event::fake();

    $invite = Invitation::to('test@email.com')->make();

    Event::assertDispatched(InvitationCreated::class);

    Event::assertDispatched(function (InvitationCreated $event) use ($invite) {
        return $event->invitation->id === $invite->id;
    });
});

it('will not allow to make an invitation without providing an email', function () {
    Invitation::make();
})->throws(EmailNotProvidedException::class);


it('will not allow to make an invitation if there is already an invitation pending with the given email', function () {
    $invite = Invitation::factory()->pending()->create();

    Invitation::to($invite->email)->make();
})->throws(EmailNotValidException::class);
