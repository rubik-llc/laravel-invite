<?php

declare(strict_types=1);

namespace Rubik\LaravelInvite\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rubik\LaravelInvite\Facades\Invite;

trait CanInvite
{
    /**
     * Defines polymorphic relation between the model that uses this trait and Invite
     * @return MorphMany
     */
    public function invites(): MorphMany
    {
        return $this->morphMany(config('invite.invite_model'), 'referable');
    }

    public function invite($email)
    {
        Invite::to($email)->referer($this)->make();
    }

}
