<?php

namespace Cone\Root\Database\Factories;

use Cone\Root\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => 'App\\Notifications\\CustomNotification',
            'data' => [
                'subject' => $this->faker->jobTitle(),
                'contnet' => $this->faker->paragraph(),
            ],
            'read_at' => Date::now(),
        ];
    }

    /**
     * Indicate that the model should be unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes): array => [
            'read_at' => null,
        ]);
    }
}
