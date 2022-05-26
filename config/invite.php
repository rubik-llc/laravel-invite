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

    'delete_on_decline' => true,

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
