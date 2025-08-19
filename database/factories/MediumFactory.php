<?php

declare(strict_types=1);

namespace Cone\Root\Database\Factories;

use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MediumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Cone\Root\Models\Medium>
     */
    protected $model = Medium::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'disk' => 'public',
            'name' => $name = Str::random(5),
            'file_name' => "{$name}.{$this->faker->fileExtension()}",
            'mime_type' => Arr::random(['image/jpg', 'application/pdf']),
            'size' => mt_rand(100, 2000),
            'width' => 1600,
            'height' => 1200,
        ];
    }
}
