<?php

namespace Cone\Root\Database\Seeders;

use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Models\User;
use Illuminate\Database\Seeder;

class RootTestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedMedia();
    }

    /**
     * Seed the user models.
     *
     * @return void
     */
    protected function seedUsers(): void
    {
        User::factory()->create([
            'name' => 'Root Admin',
            'email' => 'admin@root.local',
        ]);
    }

    /**
     * Seed the media models.
     *
     * @return void
     */
    protected function seedMedia(): void
    {
        $path = __DIR__.'/../../stubs/placeholder.png';

        foreach (range(1, 10) as $key) {
            $medium = Medium::createFrom($path);

            MoveFile::withChain([
                new PerformConversions($medium),
            ])->dispatch($medium, $path);
        }
    }
}
