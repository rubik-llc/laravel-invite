<?php

declare(strict_types=1);

namespace Rubik\LaravelInvite\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rubik\LaravelInvite\Enums\State;
use Rubik\LaravelInvite\Events\InvitationAccepted;
use Rubik\LaravelInvite\Events\InvitationCreated;
use Rubik\LaravelInvite\Events\InvitationDeclined;
use Rubik\LaravelInvite\Events\InvitationDeleted;

class Invite extends Model
{
    use HasFactory;

    protected $table = 'invites';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $appends = [
        'state',
    ];
    protected $dispatchesEvents = [
        'created' => InvitationCreated::class,
        'deleted' => InvitationDeleted::class,
    ];

    /**
     * Get invite based on token
     *
     * @param Builder $query
     * @param $token
     * @return Model|null
     */
    public function scopeWithToken(Builder $query, $token): ?Model
    {
        return $query->where('token', $token)->firstOrFail();
    }

    /**
     * Get all pending invites
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull(['accepted_at', 'declined_at'])->whereDate('expires_at', '>=', Carbon::now());
    }

    /**
     * Get all expired invites
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull(['accepted_at', 'declined_at'])->whereDate('expires_at', '<', Carbon::now());
    }

    /**
     * Get all accepted invites
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    /**
     * Get all declined invites
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDeclined(Builder $query): Builder
    {
        return $query->whereNotNull('declined_at');
    }

    /**
     * @return MorphTo
     */
    public function invitable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function referable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if an invitation is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return ! $this->isAccepted() && ! $this->isDeclined() && Carbon::parse($this->expires_at) < Carbon::now();
    }

    /**
     * Check if an invitation is accepted
     *
     * @return bool
     */
    public function isAccepted(): bool
    {
        return ! ! $this->accepted_at;
    }

    /**
     * Check if an invitation is declined
     *
     * @return bool
     */
    public function isDeclined(): bool
    {
        return ! ! $this->declined_at;
    }

    /**
     * Check if an invitation is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isDeclined() && Carbon::parse($this->expires_at) >= Carbon::now();
    }

    /**
     * Accept an invitation
     *
     * @return bool
     */
    public function accept(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        $this->update([
            'accepted_at' => Carbon::now(),
        ]);

        InvitationAccepted::dispatch($this);

        return true;
    }

    /**
     * Decline an invitation
     *
     * @return bool
     */
    public function decline(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        $this->update([
            'declined_at' => Carbon::now(),
        ]);

        InvitationDeclined::dispatch($this);

        if (config('invite.delete_on_decline')) {
            $this->delete();
        }

        return true;
    }

    /**
     * Update the expiration date of an invitation
     *
     * @param Carbon|string $date
     * @return bool
     */
    public function expireAt(Carbon|string $date): bool
    {
        if ($date instanceof Carbon) {
            $expires_at = $date;
        } else {
            $expires_at = Carbon::parse($date);
        }

        $this->update([
            'expires_at' => $expires_at,
        ]);

        return true;
    }

    /**
     * The state of an invitation
     *
     * @return Attribute
     */
    protected function state(): Attribute
    {
        return match (true) {
            $this->isAccepted() => Attribute::make(get: fn () => State::ACCEPTED),
            $this->isDeclined() => Attribute::make(get: fn () => State::DECLINED),
            $this->isExpired() => Attribute::make(get: fn () => State::EXPIRED),
            $this->isPending() => Attribute::make(get: fn () => State::PENDING),
            default => Attribute::make(get: null)
        };
    }
}
