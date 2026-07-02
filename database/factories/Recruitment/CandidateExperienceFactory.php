<?php

namespace Database\Factories\Recruitment;

use App\Models\Recruitment\CandidateExperience;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recruitment\CandidateExperience>
 */
class CandidateExperienceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CandidateExperience::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = FakerFactory::create('id_ID');
        $privateSectorJobs = [
            'Staf Administrasi', 
            'Operator Produksi', 
            'Sales Executive', 
            'Digital Marketing', 
            'Customer Service', 
            'Kasir', 
            'Staff Gudang (Warehouse)', 
            'Teknisi Maintenance', 
            'Satpam / Security', 
            'Driver Operasional', 
            'Quality Control', 
            'Junior Programmer', 
            'Desainer Grafis', 
            'Supervisor Toko', 
            'Akuntan', 
            'HRD Staff', 
            'General Affair',
            'Cleaning Service'
        ];
        return [
            'company'  => $faker->company,
            'position' => $faker->randomElement($privateSectorJobs),
            'years'    => $faker->numberBetween(1, 10),
        ];
    }
}
