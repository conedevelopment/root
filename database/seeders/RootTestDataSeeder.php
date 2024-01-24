<?php

namespace Cone\Root\Database\Seeders;

use Cone\Root\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class RootTestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
    }

    /**
     * Seed the user models.
     */
    protected function seedUsers(): void
    {
        User::proxy()->newQuery()->create([
            'name' => 'Root Admin',
            'email' => 'admin@root.local',
            'password' => Hash::make('password'),
        ]);
    }
}
