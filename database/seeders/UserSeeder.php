<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exam;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        User::factory()
            ->count(42)
            ->has(
                Exam::factory()
                        ->count(fake()->numberBetween(2, 15))
                        ->state(fn (array $attributes, User $user) => 
                            [
                                'candidate_id' => $user->id,
                                'candidate_name' => $user->name
                            ]
                        )
            )
            ->create();
    }
}