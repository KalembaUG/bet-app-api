<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JsonSeeder extends Seeder
{
    public function run(): void
    {
        $json = Storage::disk('local')->get('/json/candidate_data.json');
        $candidates = json_decode($json, true);

        if (is_array($candidates) || is_object($candidates))
        {
            foreach ($candidates as $candidate)
            {
                DB::table('exams')->insert([
                    "title" => $candidate['title'],
                    "description" => $candidate['description'],
                    "candidate_id" => $candidate['candidate_id'],
                    "candidate_name" => $candidate['candidate_name'],
                    "date" => $candidate['date'],
                    "location_name" => $candidate['location_name'],
                    "latitude" => $candidate['latitude'],
                    "longitude" => $candidate['longitude']
                ]);
            }
        }
    }
}