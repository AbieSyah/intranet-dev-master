<?php

namespace Database\Seeders;

use App\Models\Recruitment\Candidate;
use App\Models\Recruitment\CandidateEducation;
use App\Models\Recruitment\CandidateExperience;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = app(Generator::class);
        Candidate::factory()->count(100)
            ->create()
            ->each(function (Candidate $candidate) use ($faker) {
                $educationCount = rand(1, 2); 
                CandidateEducation::factory()->count($educationCount)->create([
                    'candidate_id' => $candidate->id,
                ]);
                if ($faker->boolean(90)) { 
                    $experienceCount = rand(1, 2); 
                    CandidateExperience::factory()->count($experienceCount)->create([
                        'candidate_id' => $candidate->id,
                    ]);
                }
            });
    }
}
