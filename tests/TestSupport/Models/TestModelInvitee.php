<?php

namespace Rubik\LaravelInvite\Tests\TestSupport\Models;

use Rubik\LaravelInvite\Traits\ReceivesInvitation;

class TestModelInvitee extends TestModel
{
    use ReceivesInvitation;
}
