<?php

namespace Cone\Root\Database\Factories;

use Cone\Root\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Record::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event' => $this->faker->company(),
            'description' => null,
        ];
    }
}

