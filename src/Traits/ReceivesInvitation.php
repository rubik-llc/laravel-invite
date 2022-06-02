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
    public function receivedInvitations(): MorphMany
    {
        return $this->morphMany(config('invite.invitation_model'), 'invitable');
    }

    /**
     * @return bool
     */
    public function hasPendingInvitations(): bool
    {
        return ! ! $this->receivedInvitations()->pending()->count();
    }

    /**
     * @return bool
     */
    public function hasExpiredInvitations(): bool
    {
        return ! ! $this->receivedInvitations()->expired()->count();
    }

    /**
     * @return bool
     */
    public function hasAcceptedInvitations(): bool
    {
        return ! ! $this->receivedInvitations()->accepted()->count();
    }

    /**
     * @return bool
     */
    public function hasDeclinedInvitations(): bool
    {
        return ! ! $this->receivedInvitations()->declined()->count();
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
            $this->hasPendingInvitations() => Attribute::make(get: fn () => InviteeState::PENDING),
            $this->hasExpiredInvitations() => Attribute::make(get: fn () => InviteeState::EXPIRED),
            default => Attribute::make(get: null)
        };
    }
}
