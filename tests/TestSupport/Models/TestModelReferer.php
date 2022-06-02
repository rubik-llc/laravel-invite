<?php

namespace Rubik\LaravelInvite\Tests\TestSupport\Models;

use Rubik\LaravelInvite\Traits\CanInvite;

class TestModelReferer extends TestModel
{
    use CanInvite;
}
