<?php

namespace Rubik\LaravelInvite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rubik\LaravelInvite\Invite
 * @see \Rubik\LaravelInvite\Models\Invite
 *
 */
class Invite extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-invite';
    }
}
