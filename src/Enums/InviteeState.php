<?php

namespace Rubik\LaravelInvite\Enums;

enum InviteeState: string
{
    case PENDING = 'Pending';
    case ACCEPTED = 'Accepted';
    case DECLINED = 'Declined';
    case EXPIRED = 'Expired';
}
