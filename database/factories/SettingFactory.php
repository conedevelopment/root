<?php

namespace Cone\Root\Database\Factories;

use Cone\Root\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Cone\Root\Models\Setting>
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->slug(1),
            'value' => mt_rand(10, 1000),
        ];
    }
}
