<?php

declare(strict_types=1);

namespace Rubik\LaravelInvite\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rubik\LaravelInvite\Enums\InviteeState;

trait ReceivesInvitation
{
    /**
     * Defines polymorphic relation between the model that uses this trait and Invitation
     * @return MorphMany
     */
    public function invitations(): MorphMany
    {
        return $this->morphMany(config('invite.invitation_model'), 'invitable');
    }

    /**
     * @return bool
     */
    public function hasPendingInvitations(): bool
    {
        return $this->invitations()->pending() > 0;
    }

    /**
     * @return bool
     */
    public function hasExpiredInvitations(): bool
    {
        return $this->invitations()->expired() > 0;
    }

    /**
     * @return bool
     */
    public function hasAcceptedInvitations(): bool
    {
        return $this->invitations()->accepted() > 0;
    }

    /**
     * @return bool
     */
    public function hasDeclinedInvitations(): bool
    {
        return $this->invitations()->declined() > 0;
    }

    /**
     * The state of an invitation
     *
     * @return Attribute
     */
    protected function state(): Attribute
    {
        return match (true) {
            $this->hasAcceptedInvitations() => Attribute::make(get: fn () => InviteeState::ACCEPTED),
            $this->hasDeclinedInvitations() => Attribute::make(get: fn () => InviteeState::DECLINED),
            $this->hasPendingInvitations() => Attribute::make(get: fn () => InviteeState::EXPIRED),
            $this->hasExpiredInvitations() => Attribute::make(get: fn () => InviteeState::PENDING),
            default => Attribute::make(get: null)
        };
    }
}
