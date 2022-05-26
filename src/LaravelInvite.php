<?php

namespace Rubik\LaravelInvite;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LaravelInvite
{
    /**
     * @var string
     */
    public string $to;

    /**
     * @var Carbon
     */
    public Carbon $expires_at;

    /**
     * @var Model
     */
    public Model $referer;

    /**
     * @var string|Model
     */
    public Model|string $invitee;


    /**
     * @param string $email
     * @return LaravelInvite
     */
    public function to(string $email): static
    {
        $this->to = $email;

        return $this;
    }

    /**
     * @param Model $referer
     * @return $this
     */
    public function referer(Model $referer): static
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * @param Model|string $invitee
     * @return $this
     */
    public function invitee(Model|string $invitee): static
    {
        $this->invitee = $invitee;

        return $this;
    }

    /**
     * @param int $hours
     * @return LaravelInvite
     */
    public function expireIn(int $hours): static
    {
        $this->expires_at = Carbon::now()->addHours($hours);

        return $this;
    }

    /**
     * @param Carbon|string $date
     * @return LaravelInvite
     */
    public function expireAt(Carbon|string $date): static
    {
        if ($date instanceof Carbon) {
            $this->expires_at = $date;
        } else {
            $this->expires_at = Carbon::parse($date);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        return md5(Str::uuid());
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $inviteModel = config('invite.invite_model');

        return !$inviteModel::pending()->where('email', $this->to)->exists();
    }

    /**
     * @return mixed|null
     */
    public function getRefererKey(): mixed
    {
        if (isset($this->referer)) return $this->referer->getKey();

        return null;
    }

    /**
     * @return string|null
     */
    public function getRefererClass(): ?string
    {
        if (isset($this->referer)) return get_class($this->referer);

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getInviteeKey(): mixed
    {
        if (isset($this->invitee) && $this->invitee instanceof Model) return $this->invitee->getKey();

        return null;
    }

    /**
     * @return string|null
     */
    public function getInviteeClass(): ?string
    {
        if (isset($this->invitee) && $this->invitee instanceof Model) return get_class($this->invitee);

        return $this->invitee ?? null;
    }

    /**
     * @throws \Exception
     */
    public function make()
    {
        $inviteModel = config('invite.invite_model');

        if (!isset($this->to)) {
            throw new \Exception('You need to provide an email!');
        }

        if (!$this->isValid()) {
            throw new \Exception('Email is not valid!');
        }

        return $inviteModel::create([
            'email' => $this->to,
            'token' => $this->generateToken(),
            'expires_at' => $this->expires_at ?? Carbon::now()->addHours(config('invite.expire.after')),
            'referable_id' => $this->getRefererKey(),
            'referable_type' => $this->getRefererClass(),
            'invitable_id' => $this->getInviteeKey(),
            'invitable_type' => $this->getInviteeClass(),
        ]);
    }

}
