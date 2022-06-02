<?php

use Illuminate\Support\Collection;
use Rubik\LaravelInvite\Models\Invitation;
use Rubik\LaravelInvite\Tests\TestSupport\Models\TestModelReferer;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertInstanceOf;

beforeEach(function () {
    $this->testModel = createTestModel(TestModelReferer::class);
});

it('has invitations', function () {
    Invitation::factory()->for($this->testModel, 'referable')->create();

    assertInstanceOf(Collection::class, $this->testModel->referredInvitations);
    expect($this->testModel->referredInvitations->count())->toBe(1);
});

it('can make invitations', function () {
    $this->testModel->invitation()->to('test@email.com')->make();

    assertDatabaseHas('invitations', ['referable_id' => $this->testModel->id, 'referable_type' => TestModelReferer::class]);
    expect(Invitation::count())->toBe(1);
});
