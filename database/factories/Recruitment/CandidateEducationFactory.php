<?php

namespace Database\Factories\Recruitment;

use App\Models\Recruitment\CandidateEducation;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recruitment\CandidateEducation>
 */
class CandidateEducationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CandidateEducation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = FakerFactory::create('id_ID');
        $internalType = $faker->randomElement([
            'SMA', 
            'SMK', 
            'Diploma Degree', 
            'Bachelor Degree', 
            'Profession Program'
        ]);
        $levelDB = $internalType;
        $prefix = 'Universitas';
        $majors = [
            'Teknik Elektro', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Kimia', 
            'Teknik Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi', 
            'Hukum', 'Psikologi', 'Ilmu Komunikasi', 'Kedokteran', 'Farmasi'
        ];
        $isSchool = false;
        if ($internalType === 'SMA') {
            $levelDB = 'Senior High School';
            $prefix = 'SMA Negeri '.$faker->numberBetween(1,3);
            $majors = ['IPA', 'IPS', 'Bahasa'];
            $isSchool = true;
        } elseif ($internalType === 'SMK') {
            $levelDB = 'Senior High School';
            $prefix = 'SMK Negeri '.$faker->numberBetween(1,3);
            $majors = [
                'Teknik Komputer Jaringan', 
                'Rekayasa Perangkat Lunak', 
                'Multimedia', 
                'Akuntansi', 
                'Administrasi Perkantoran', 
                'Teknik Kendaraan Ringan',
                'Teknik Pemesinan'
            ];
            $isSchool = true;
        }
        $institution = $prefix . ' ' . $faker->city;
        $scoreGpa = $isSchool ? 
                    $faker->randomFloat(2, 70, 98) : 
                    $faker->randomFloat(2, 2.75, 4.00);
        return [
            'level'            => $levelDB,
            'institution_name' => $institution,
            'major'            => $faker->randomElement($majors),
            'year_graduated'   => $faker->numberBetween(2015, 2024),
            'score_gpa'        => $scoreGpa,
        ];
    }
}
