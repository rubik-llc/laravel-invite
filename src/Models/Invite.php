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

class Invite extends Model
{
    use HasFactory;

    protected $table = 'invites';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $appends = [
        'state'
    ];

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

    public function isExpired(): bool
    {
        return !$this->isAccepted() && !$this->isDeclined() && Carbon::parse($this->expires_at) < Carbon::now();
    }

    public function isAccepted(): bool
    {
        return !!$this->accepted_at;
    }

    public function isDeclined(): bool
    {
        return !!$this->declined_at;
    }

    public function isPending(): bool
    {
        return !$this->isAccepted() && !$this->isDeclined() && Carbon::parse($this->expires_at) >= Carbon::now();
    }

//    public static function make(string $email, Model $referer = null, Model $invitee = null, int|string|Carbon $expire = null)
//    {
//        $user = User::find(1);
//        Invite::to('email')->referer(auth()->user())->invitee($user)->expireIn()->expireAt()
//
//        self::create([
//            'email' => $email,
//            'token' => 'askjldhjaskhdajskldaskjdhasd',
//            'expires_at' => Carbon::now(),
//        ]);
//    }


    /**
     * Accept an invitation
     *
     * @return bool
     */
    public function accept(): bool
    {
        if ($this->isPending()) {
            $this->update([
                'accepted_at' => Carbon::now(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Decline an invitation
     *
     * @return bool
     */
    public function decline(): bool
    {
        if ($this->isPending()) {
            $this->update([
                'declined_at' => Carbon::now(),
            ]);

            return true;
        }

        return false;
    }

    protected function state(): Attribute
    {
        return match (true) {
            $this->isAccepted() => Attribute::make(get: fn() => State::ACCEPTED),
            $this->isDeclined() => Attribute::make(get: fn() => State::DECLINED),
            $this->isExpired() => Attribute::make(get: fn() => State::EXPIRED),
            $this->isPending() => Attribute::make(get: fn() => State::PENDING),
            default => Attribute::make(get: null)
        };
    }
}
