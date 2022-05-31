<?php

namespace Rubik\LaravelInvite;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Rubik\LaravelInvite\Exceptions\EmailNotProvidedException;
use Rubik\LaravelInvite\Exceptions\EmailNotValidException;

class Invite
{
    /**
     * Invite model
     *
     * @var string
     */
    protected string $model;

    /**
     * @var string
     */
    protected string $to;

    /**
     * @var Carbon
     */
    protected Carbon $expires_at;

    /**
     * @var Model
     */
    protected Model $referer;

    /**
     * @var string|Model
     */
    protected Model|string $invitee;

    public function __construct()
    {
        $this->model = config('invite.invite_model');
    }

    /**
     * @param string $email
     * @return Invite
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
     * @param int $value
     * @param $unit
     * @return Invite
     */
    public function expireIn(int $value, $unit = null): static
    {
        $unit = $unit ?: config('invite.unit');
        $this->expires_at = Carbon::now()->add($value, $unit);

        return $this;
    }

    /**
     * @param Carbon|string $date
     * @return Invite
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
        return !$this->model::pending()->where('email', $this->to)->exists();
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
        $this->validate();

        $invitation = $this->model::create([
            'email' => $this->to,
            'token' => $this->generateToken(),
            'expires_at' => $this->expires_at ?? Carbon::now()->add(config('invite.expire.after'), config('invite.unit')),
            'referable_id' => $this->getRefererKey(),
            'referable_type' => $this->getRefererClass(),
            'invitable_id' => $this->getInviteeKey(),
            'invitable_type' => $this->getInviteeClass(),
        ]);

        $this->forgetAll();

        return $invitation;
    }

    /**
     * @return void
     */
    public function forgetAll(): void
    {
        $this->forgetTo();
        $this->forgetExpiresAt();
        $this->forgetReferer();
        $this->forgetInvitee();
    }

    /**
     * @return void
     */
    public function forgetTo(): void
    {
        unset($this->to);
    }

    /**
     * @return void
     */
    public function forgetExpiresAt(): void
    {
        unset($this->expires_at);
    }

    /**
     * @return void
     */
    public function forgetReferer(): void
    {
        unset($this->referer);
    }

    /**
     * @return void
     */
    public function forgetInvitee(): void
    {
        unset($this->invitee);
    }

    /**
     * @return void
     * @throws EmailNotProvidedException|EmailNotValidException
     */
    public function validate(): void
    {
        if (!isset($this->to)) {
            throw EmailNotProvidedException::make();
        }

        if (!$this->isValid()) {
            throw EmailNotValidException::make();
        }
    }

    public function __call(string $method, array $parameters)
    {
        $instance = new $this->model;
        return $instance->$method(...$parameters);
    }

}
