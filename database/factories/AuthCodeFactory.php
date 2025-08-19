<?php

declare(strict_types=1);

namespace Cone\Root\Database\Factories;

use Cone\Root\Models\AuthCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class AuthCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Cone\Root\Models\AuthCode>
     */
    protected $model = AuthCode::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => mt_rand(100000, 999999),
            'expires_at' => Date::now()->addMinutes(5),
        ];
    }

    /**
     * Indicate that the model should be expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => Date::now()->subMinute(),
        ]);
    }
}
