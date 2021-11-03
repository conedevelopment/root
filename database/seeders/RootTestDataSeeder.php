<?php

namespace Cone\Root\Database\Seeders;

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
        User::factory()->create([
            'name' => 'Root Admin',
            'email' => 'admin@root.local',
        ]);
    }
}
