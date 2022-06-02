<?php

declare(strict_types=1);

namespace Rubik\LaravelInvite\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rubik\LaravelInvite\Facades\Invitation;

trait CanInvite
{
    /**
     * Defines polymorphic relation between the model that uses this trait and Invitation
     * @return MorphMany
     */
    public function referredInvitations(): MorphMany
    {
        return $this->morphMany(config('invite.invitation_model'), 'referable');
    }

    /**
     * @return mixed
     */
    public function invitation(): mixed
    {
        return Invitation::referer($this);
    }

}
