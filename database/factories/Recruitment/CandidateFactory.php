<?php

namespace Database\Factories\Recruitment;

use App\Models\Area;
use App\Models\Department;
use App\Models\Position;
use App\Models\Recruitment\Candidate;
use App\Models\Recruitment\JobPosting;
use App\Models\Section;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Candidate::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = FakerFactory::create('id_ID');
        $genderValue = $this->faker->randomElement(['Male', 'Female']);
        $fakerGender = $genderValue == 'Male' ? 'male' : 'female';
        $jobPosting = JobPosting::where('status', 'PUBLISH')->inRandomOrder()->first();
        $startDate = '-1 months';
        $endDate   = 'now';
        if ($jobPosting) {
            $startDate = $jobPosting->apply_start;
            $endDate   = $jobPosting->apply_end;
        }
        $postingId = $jobPosting->id ?? 1;
        $positionId = $jobPosting ? ($jobPosting->position_id ?? null) : 1;
        $departmentId = $jobPosting ? ($jobPosting->department_id ?? null) : 1;
        $sectionId = $jobPosting ? ($jobPosting->section_id ?? null) : null;
        $areaId = $jobPosting ? ($jobPosting->area_id ?? null) : 1;
        $allowedEmails = [
            'erlanggalesmanaputra@gmail.com',
            'kelompok2polije@gmail.com',
            'e41221172@student.polije.ac.id',
        ];
        $availableSkills = [
            'Microsoft Office', 
            'Public Speaking', 
            'Leadership', 
            'Teamwork', 
            'Time Management', 
            'Bahasa Inggris', 
            'Digital Marketing', 
            'Adobe Photoshop', 
            'Akuntansi', 
            'Negosiasi', 
            'Pemrograman Web', 
            'Analisis Data', 
            'Copywriting', 
            'Manajemen Proyek', 
            'Customer Service'
        ];
        $firstName = $this->faker->firstName($fakerGender);
        $lastName  = $this->faker->lastName();
        return [
            'posting_id'        => $postingId,
            'position_id'       => $positionId,
            'department_id'     => $departmentId,
            'section_id'        => $sectionId,
            'area_id'           => $areaId,

            'no_ktp'            => $faker->nik(),
            'fullname'          => $firstName.' '.$lastName,
            'nickname'          => $firstName,
            'ktp_address'       => $faker->address,
            'domicile_address'  => $faker->boolean(70) ? $faker->address : $faker->address,
            'phone'             => $faker->phoneNumber,
            'email'             => $faker->randomElement($allowedEmails),
            'birthplace'        => $faker->city,
            'birthdate'         => $faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'gender'            => $genderValue,
            'religion'          => $faker->randomElement(['Moslem', 'Catholic', 'Christian', 'Budhist', 'Hindu', 'None']), 
            'marital'           => $faker->randomElement(['Single', 'Married', 'Divorced', 'Widow', 'Widower']), 
            'height'            => $faker->numberBetween(150, 180), 
            'weight'            => $faker->numberBetween(45, 85), 
            'skill'             => implode(', ', $faker->randomElements($availableSkills, $faker->numberBetween(3, 5))),
            'submit_date'       => $faker->dateTimeBetween($startDate, $endDate),
        ];
    }
}
