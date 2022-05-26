<?php

namespace Rubik\LaravelInvite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rubik\LaravelInvite\LaravelInvite
 */
class LaravelInvite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Rubik\LaravelInvite\LaravelInvite::class;
    }
}
