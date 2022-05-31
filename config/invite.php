<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invitation class
    |--------------------------------------------------------------------------
    |
    | The invite class that should be used to store and retrieve the invitations.
    | If you specify a different model class, make sure that model extends the default
    | Invitation model that is shipped with this package.
    |
    */

    'invitation_model' => \Rubik\LaravelInvite\Models\Invitation::class,

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
    | The default value of when to expire an invitation after its created. It uses
    | the units that are specified above.
    |
    | If the delete.auto value is set to true, it enables a scheduler that executes
    | a command every hour which deletes all invitations that have surpassed the amount
    | of time given in delete.after
    |
    */
    'expire' => [

        'after' => 48,

        'delete' => [
            'auto' => false,
            'after' => 48,
        ],

    ],

];
