<?php

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
     * @param $query
     * @return mixed
     */
    public function scopePending($query): Builder
    {
        return $query->whereNull(['accepted_at', 'declined_at'])->whereDate('expires_at', '>=', Carbon::now());
    }

    /**
     * Get all expired invites
     *
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query): Builder
    {
        return $query->whereNull(['accepted_at', 'declined_at'])->whereDate('expires_at', '<', Carbon::now());
    }

    /**
     * Get all accepted invites
     *
     * @param $query
     * @return mixed
     */
    public function scopeAccepted($query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    /**
     * Get all declined invites
     *
     * @param $query
     * @return mixed
     */
    public function scopeDeclined($query): Builder
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

    protected function state(): Attribute
    {
        if ($this->isAccepted()) return Attribute::make(get: fn() => State::ACCEPTED);
        if ($this->isDeclined()) return Attribute::make(get: fn() => State::DECLINED);
        if ($this->isExpired()) return Attribute::make(get: fn() => State::EXPIRED);
        if ($this->isPending()) return Attribute::make(get: fn() => State::PENDING);

        return Attribute::make(get: null);
    }
}
