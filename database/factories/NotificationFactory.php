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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'type' => 'App\\Notifications\\TestNotification',
            'data' => [
                'content' => $this->faker->text(),
                'title' => $this->faker->words(5, true),
            ],
        ];
    }

    /**
     * Define the model's unread state.
     *
     * @return \Cone\Root\Database\Factories\NotificationFactory
     */
    public function read(): self
    {
        return $this->state(function (array $attributes): array {
            return [
                'read_at' => Date::now(),
            ];
        });
    }
}
