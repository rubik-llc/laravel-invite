<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invite class
    |--------------------------------------------------------------------------
    |
    | The invite class that should be used to store and retrieve the invites.
    | If you specify a different model class, make sure that model extends the default
    | Invite model that is shipped with this package.
    |
    */

    'invite_model' => \Rubik\LaravelInvite\Models\Invite::class,

    /*
    |--------------------------------------------------------------------------
    | Delete on decline
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, whenever an invitation is declined it will automatically
    | be deleted.
    |
    */

    'delete_on_decline' => false,

    /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    |
    | The unit of the values.
    | This package uses Carbon for date and time related calculations, therefore
    | the value of this option should be only values that Carbon accepts.
    | e.g: seconds, minutes, hours, days, weeks, months, years, etc.
    |
    */

    'unit' => 'hours',

    /*
    |--------------------------------------------------------------------------
    | Expire
    |--------------------------------------------------------------------------
    |
    */

    'expire' => [

        'after' => 48,

        'delete' => [
            'auto' => true,
            'after' => 24,
        ],

        //duhet me kqyr apet
        're-invite' => [
//            'event' => null,
            'auto' => true,
            'after' => 24,
        ]
    ],

];
