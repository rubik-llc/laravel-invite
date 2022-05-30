<?php

namespace Rubik\LaravelInvite\Exceptions;

use Exception;


class EmailNotProvidedException extends Exception
{
    /**
     * @return static
     */
    public static function make(): static
    {
        return new static('You need to provide an email!');
    }
}
