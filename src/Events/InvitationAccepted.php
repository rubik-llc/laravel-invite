<?php

namespace Rubik\LaravelInvite\Events;


use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rubik\LaravelInvite\Models\Invite;

class InvitationAccepted
{
    use Dispatchable, SerializesModels;

    public Invite $invitation;

    /**
     *
     * @return void
     */
    public function __construct(Invite $invitation)
    {
        $this->invitation = $invitation;
    }

}
