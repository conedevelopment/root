<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        $factory = new class() extends Factory
        {
            protected $model = Team::class;

            public function definition(): array
            {
                return [
                    'name' => $this->faker->company(),
                ];
            }
        };

        return $factory->configure();
    }
}
