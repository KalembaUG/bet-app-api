<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::factory()
            ->count(50)
            ->create();
    }
}