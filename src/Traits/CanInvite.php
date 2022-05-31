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
    public function invitations(): MorphMany
    {
        return $this->morphMany(config('invite.invitation_model'), 'referable');
    }

    /**
     * @param $email
     * @return mixed
     */
    public function invite($email): mixed
    {
        return Invitation::to($email)->referer($this)->make();
    }

    /**
     * @param $email
     * @param $model
     * @return mixed
     */
    public function inviteModel($email, $model): mixed
    {
        return Invitation::to($email)->referer($this)->invitee($model)->make();
    }
}
