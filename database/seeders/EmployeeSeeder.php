<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Level;
use App\Models\Master\Building;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $areaIds = Area::query()->pluck('id')->all();
        $departmentIds = Department::query()->pluck('id')->all();
        $sectionIds = Section::query()->pluck('id')->all();
        $positionIds = Position::query()->pluck('id')->all();
        $levelIds = Level::query()->pluck('id')->all();
        $buildingIds = Building::query()->pluck('id')->all();

        if (empty($areaIds) || empty($departmentIds) || empty($sectionIds) || empty($positionIds) || empty($levelIds) || empty($buildingIds)) {
            $this->command?->warn('Master data belum lengkap. Jalankan DemoDataSeeder terlebih dahulu.');
            return;
        }

        $firstNames = [
            'Ahmad', 'Siti', 'Budi', 'Dewi', 'Eko', 'Fitri', 'Guntur', 'Hani', 'Iwan', 'Julia',
            'Kevin', 'Laras', 'Muhammad', 'Nadia', 'Oki', 'Putri', 'Rian', 'Siska', 'Taufik', 'Vina',
            'Wahyu', 'Yuni', 'Zainal', 'Anisa', 'Bambang', 'Citra', 'Dedi', 'Erlina', 'Fajar', 'Gita',
        ];

        $lastNames = [
            'Pratama', 'Lestari', 'Santoso', 'Utami', 'Prasetyo', 'Wulandari', 'Wibowo', 'Handayani', 'Setiawan', 'Permata',
            'Sanjaya', 'Dewi', 'Rizky', 'Amelia', 'Oktavian', 'Rahayu', 'Hidayat', 'Amelia', 'Kurniawan', 'Maulana',
            'Hidayat', 'Shara', 'Abidin', 'Rahma', 'Pamungkas', 'Scholastika', 'Corbuzier', 'Lestari', 'Aji', 'Mulia',
        ];

        $cities = [
            'Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Medan', 'Yogyakarta', 'Bogor', 'Bekasi', 'Palembang', 'Makassar',
            'Denpasar', 'Solo', 'Malang', 'Balikpapan', 'Banjarmasin', 'Palu', 'Jember', 'Tangerang', 'Depok', 'Sidoarjo',
        ];

        $religions = ['Islam', 'Christian', 'Catholic', 'Hindu', 'Buddha'];
        $genders = ['Male', 'Female'];
        $maritals = ['Single', 'Married'];
        $statuses = ['Permanent', 'Contract'];
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        $workLocations = ['Head Office', 'Branch Office'];
        $taxDependents = ['TK0', 'TK1', 'K0', 'K1', 'K2', 'K3'];
        $bankNames = ['BCA', 'Mandiri', 'BNI', 'BRI'];

        for ($index = 1; $index <= 100; $index++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullname = trim($firstName . ' ' . $lastName . ' ' . $index);
            $nik = 'DUMMY-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT);
            $email = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName)) . $index . '@email.com';
            $city = $cities[array_rand($cities)];
            $gender = $genders[array_rand($genders)];
            $status = $statuses[array_rand($statuses)];
            $joindate = $now->copy()->subDays(rand(30, 1500))->toDateString();

            Employee::updateOrCreate(
                ['nik' => $nik],
                [
                    'no_ktp' => '32' . str_pad((string) rand(10000000000000, 99999999999999), 14, '0', STR_PAD_LEFT),
                    'fullname' => $fullname,
                    'email' => $email,
                    'addressktp' => 'Jl. Contoh No. ' . $index . ', ' . $city,
                    'birthplace' => $city,
                    'birthdate' => $now->copy()->subYears(rand(22, 45))->subDays(rand(0, 365))->toDateString(),
                    'gender' => $gender,
                    'religion' => $religions[array_rand($religions)],
                    'marital' => $maritals[array_rand($maritals)],
                    'hp' => '08' . str_pad((string) rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                    'joindate' => $joindate,
                    'status' => $status,
                    'area_id' => $areaIds[array_rand($areaIds)],
                    'department_id' => $departmentIds[array_rand($departmentIds)],
                    'section_id' => $sectionIds[array_rand($sectionIds)],
                    'position_id' => $positionIds[array_rand($positionIds)],
                    'level_id' => $levelIds[array_rand($levelIds)],
                    'building_id' => $buildingIds[array_rand($buildingIds)],
                    'work_location' => $workLocations[array_rand($workLocations)],
                    'contract_startdate' => $status === 'Contract' ? $joindate : null,
                    'domicile_address' => 'Jl. Domisili No. ' . $index . ', ' . $city,
                    'tax_dependents' => $taxDependents[array_rand($taxDependents)],
                    'npwp' => '01' . str_pad((string) $index, 14, '0', STR_PAD_LEFT),
                    'bank_name' => $bankNames[array_rand($bankNames)],
                    'bank_account' => (string) rand(1000000000, 9999999999),
                    'bank_account_holder' => $fullname,
                    'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
