<?php

use Carbon\Carbon;
use Rubik\LaravelInvite\Facades\Invite;
use Rubik\LaravelInvite\Tests\TestSupport\Models\TestModel;
use Rubik\LaravelInvite\Tests\TestSupport\Models\User;
use function Spatie\PestPluginTestTime\testTime;

it('can return specific type of invites', function ($data, $value) {
    Invite::factory()->pending()->create();
    Invite::factory()->pending()->create();
    Invite::factory()->expired()->create();
    Invite::factory()->accepted()->create();
    Invite::factory()->declined()->create();
    Invite::factory()->declined()->create();
    Invite::factory()->declined()->create();

    expect(Invite::$data()->count())->toBe($value);
    expect(Invite::count())->toBe(7);
})->with(
    [
        'pending invites' => ['pending', 2],
        'expired invites' => ['expired', 1],
        'accepted invites' => ['accepted', 1],
        'declined invites' => ['declined', 3],
    ]
);

it('can retrieve an invite by its token', function () {
    $invite = Invite::factory()->create();

    expect(Invite::withToken($invite->token)->token)->toBe($invite->token);
    expect(Invite::withToken($invite->token))->not()->toBeNull();

});


it('can determine if an invite is pending', function ($data, $value) {
    expect($data->isPending())->toBe($value);
})->with(
    [
        'invite is pending' => [fn() => Invite::factory()->pending()->create(), true],
        'invite is expired' => [fn() => Invite::factory()->expired()->create(), false],
        'invite is accepted' => [fn() => Invite::factory()->accepted()->create(), false],
        'invite is declined' => [fn() => Invite::factory()->declined()->create(), false],
    ]
);

it('can determine if an invite is expired', function ($data, $value) {
    expect($data->isExpired())->toBe($value);
})->with(
    [
        'invite is pending' => [fn() => Invite::factory()->pending()->create(), false],
        'invite is expired' => [fn() => Invite::factory()->expired()->create(), true],
        'invite is accepted' => [fn() => Invite::factory()->accepted()->create(), false],
        'invite is expired and accepted' => [fn() => Invite::factory()->expired()->accepted()->create(), false],
        'invite is declined' => [fn() => Invite::factory()->declined()->create(), false],
        'invite is expired and declined' => [fn() => Invite::factory()->expired()->declined()->create(), false],

    ]
);

it('can determine if an invite is accepted', function ($data, $value) {
    expect($data->isAccepted())->toBe($value);
})->with(
    [
        'invite is pending' => [fn() => Invite::factory()->pending()->create(), false],
        'invite is expired' => [fn() => Invite::factory()->expired()->create(), false],
        'invite is accepted' => [fn() => Invite::factory()->accepted()->create(), true],
        'invite is expired and accepted' => [fn() => Invite::factory()->expired()->accepted()->create(), true],
        'invite is declined' => [fn() => Invite::factory()->declined()->create(), false],
        'invite is expired and declined' => [fn() => Invite::factory()->expired()->declined()->create(), false],
    ]
);

it('can determine if an invite is declined', function ($data, $value) {
    expect($data->isDeclined())->toBe($value);
})->with(
    [
        'invite is pending' => [fn() => Invite::factory()->pending()->create(), false],
        'invite is expired' => [fn() => Invite::factory()->expired()->create(), false],
        'invite is accepted' => [fn() => Invite::factory()->accepted()->create(), false],
        'invite is expired and accepted' => [fn() => Invite::factory()->expired()->accepted()->create(), false],
        'invite is declined' => [fn() => Invite::factory()->declined()->create(), true],
        'invite is expired and declined' => [fn() => Invite::factory()->expired()->declined()->create(), true],
    ]
);

it('belongs to referable', function (string $model) {
    $invite = Invite::factory()->for($model::factory(), 'referable')->create();
    $this->assertInstanceOf($model, $invite->referable);
})->with([
    [User::class],
    [TestModel::class],
]);

it('belongs to invitable', function (string $model) {
    $invite = Invite::factory()->for($model::factory(), 'invitable')->create();
    $this->assertInstanceOf($model, $invite->invitable);
})->with([
    [User::class],
    [TestModel::class],
]);

it('can be accepted', function () {
    testTime()->freeze();

    $invite = Invite::factory()->pending()->create();
    expect($invite->refresh()->accepted_at)->toBeNull();

    $invite->accept();
    expect($invite->refresh()->accepted_at)->toBe(Carbon::now()->format('Y-m-d H:i:s'));
});

it('can be declined', function () {
    testTime()->freeze();

    $invite = Invite::factory()->pending()->create();
    expect($invite->refresh()->declined_at)->toBeNull();

    $invite->decline();
    expect($invite->refresh()->declined_at)->toBe(Carbon::now()->format('Y-m-d H:i:s'));
});


it('test', function () {

//    Invite::factory()->pending()->create();

    Invite::to('rroni.nela@gmail.com')->expireIn(5)->make();

    dd(Invite::first());
});
