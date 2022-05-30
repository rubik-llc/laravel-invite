<?php

namespace Rubik\LaravelInvite\Exceptions;

use Exception;

class EmailNotValidException extends Exception
{
    /**
     * @return static
     */
    public static function make(): static
    {
        return new static('Email is not valid!');
    }
}
