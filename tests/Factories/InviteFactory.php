<?php

namespace Rubik\LaravelInvite\Tests\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Rubik\LaravelInvite\Models\Invite;

class InviteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'token' => $this->faker->uuid(),
            'expires_at' => $this->faker->dateTimeBetween('-1 day', '+1 day'),
            'accepted_at' => null,
            'declined_at' => null,
            'invitable_id' => null,
            'invitable_type' => null,
            'referable_id' => null,
            'referable_type' => null,
        ];
    }

    /**
     * Sets the accepted at column to a given date or current date if not provided
     *
     * @param null $date
     * @return Factory
     */
    public function accepted($date = null): Factory
    {
        return $this->state([
            'accepted_at' => $date ? Carbon::parse($date) : Carbon::now(),
        ]);
    }

    /**
     * Sets the declined at column to a given date or current date if not provided
     *
     * @param null $date
     * @return Factory
     */
    public function declined($date = null): Factory
    {
        return $this->state([
            'declined_at' => $date ? Carbon::parse($date) : Carbon::now(),
        ]);
    }

    /**
     * Sets the expired at column to yesterday
     *
     * @return Factory
     */
    public function expired(): Factory
    {
        return $this->state([
            'expires_at' => Carbon::yesterday(),
        ]);
    }

    /**
     * Sets the expired at column to a given date
     *
     * @param $date
     * @return Factory
     */
    public function expiresAt($date): Factory
    {
        if ($date instanceof Carbon) {
            return $this->state([
                'expires_at' => $date,
            ]);
        }

        return $this->state([
            'expires_at' => Carbon::parse($date),
        ]);
    }

    /**
     * Sets the expired at column to tomorrow
     *
     * @return Factory
     */
    public function pending(): Factory
    {
        return $this->state([
            'expires_at' => Carbon::tomorrow(),
        ]);
    }
}
