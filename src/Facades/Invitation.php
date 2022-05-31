<?php

namespace Rubik\LaravelInvite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rubik\LaravelInvite\Invitation
 * @see \Rubik\LaravelInvite\Models\Invitation
 *
 */
class Invitation extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-invite';
    }
}
