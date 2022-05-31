<?php

namespace Rubik\LaravelInvite\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rubik\LaravelInvite\Models\Invitation;

class InvitationAccepted
{
    use Dispatchable;
    use SerializesModels;

    public Invitation $invitation;

    /**
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }
}
