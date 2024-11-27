<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\QuestionsTableSeeder as SeedersQuestionsTableSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SeedersQuestionsTableSeeder::class);
        
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'password' => 'bot',
        ]);
    }
}
