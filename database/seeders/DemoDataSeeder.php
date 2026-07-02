<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Area;
use App\Models\Calendar;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Level;
use App\Models\Master\Building;
use App\Models\Master\Contract;
use App\Models\Master\LineApproval;
use App\Models\Position;
use App\Models\Section;
use App\Models\Tempcalendar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAreas();
        $this->seedDepartments();
        $this->seedMasters();
        $this->seedLeaves();
        $this->seedUsersAndEmployees();
        $this->seedAbout();
        $this->seedCalendar();
        $this->seedLineApproval();
    }

    private function seedAreas(): void
    {
        $areas = [
            1 => ['kode' => 'HQF', 'name' => 'HEADQUARTERS / FACTORY'],
            2 => ['kode' => 'HO', 'name' => 'HEAD OFFICE'],
            3 => ['kode' => 'EJ1', 'name' => 'EJ1'],
            4 => ['kode' => 'EJ2', 'name' => 'EJ2'],
            5 => ['kode' => 'WJ', 'name' => 'WEST JAVA'],
            6 => ['kode' => 'JKT1', 'name' => 'JAKARTA 1'],
            7 => ['kode' => 'OIWJ1', 'name' => 'OIWJ1'],
            8 => ['kode' => 'OIWJ2', 'name' => 'OIWJ2'],
            9 => ['kode' => 'MDN', 'name' => 'MEDAN'],
            10 => ['kode' => 'KLM', 'name' => 'KALIMANTAN'],
            11 => ['kode' => 'MKS', 'name' => 'MAKASSAR'],
            12 => ['kode' => 'CJ1', 'name' => 'CJ1'],
            13 => ['kode' => 'CJ2', 'name' => 'CJ2'],
            19 => ['kode' => 'JKT2', 'name' => 'JAKARTA 2'],
            20 => ['kode' => 'OIE', 'name' => 'OIE'],
        ];

        foreach ($areas as $id => $data) {
            Area::updateOrCreate(['id' => $id], $data);
        }
    }

    private function seedDepartments(): void
    {
        $departments = [
            1 => ['name' => 'HRD & GA', 'approval' => 1, 'approval_code' => 2],
            2 => ['name' => 'ACC & FIN', 'approval' => 1, 'approval_code' => 2],
            3 => ['name' => 'HSE', 'approval' => 1, 'approval_code' => 1],
            4 => ['name' => 'Production', 'approval' => 2, 'approval_code' => 1],
            5 => ['name' => 'Quality', 'approval' => 2, 'approval_code' => 1],
            6 => ['name' => 'Kaizen Development', 'approval' => 2, 'approval_code' => 1],
            7 => ['name' => 'Sales & Trade Marketing', 'approval' => 1, 'approval_code' => null],
            8 => ['name' => 'Engineering', 'approval' => 2, 'approval_code' => 1],
            9 => ['name' => 'Warehouse & Logistic', 'approval' => 2, 'approval_code' => 1],
            10 => ['name' => 'Purchasing', 'approval' => 2, 'approval_code' => 1],
            11 => ['name' => 'NA', 'approval' => 1, 'approval_code' => null],
            12 => ['name' => 'BOD', 'approval' => 1, 'approval_code' => null],
            13 => ['name' => 'Marketing', 'approval' => 1, 'approval_code' => null],
            18 => ['name' => 'Regulatory', 'approval' => null, 'approval_code' => null],
        ];

        foreach ($departments as $id => $data) {
            Department::updateOrCreate(['id' => $id], $data);
        }
    }

    private function seedMasters(): void
    {
        $positions = [
            1 => 'OPERATOR',
            2 => 'ADVISOR',
            3 => 'ASSISTANT MANAGER',
            4 => 'KARU/KASI',
            5 => 'GROUP LEADER PRODUCTION',
            6 => 'SALES AREA MANAGER',
            7 => 'SALES & TRADE MARKETING SENIOR GENERAL MANAGER',
            8 => 'MARKETING LOGISTIC & OFFICE STAFF',
            9 => 'MARKETING ASSISTANT',
            10 => 'TECHNICIAN',
        ];

        $sections = [
            1 => 'ENGINEERING',
            2 => 'PACKING',
            3 => 'NA',
            4 => 'BUNBURY 75',
            5 => 'CALENDER',
            6 => 'CUTTING',
            7 => 'FILLING',
            8 => 'LINGELCREAM',
            9 => 'QUALITY CONTROL',
            10 => 'WEIGHING',
        ];

        $levels = [
            1 => 'OPERATOR',
            2 => 'MANAGER',
            3 => 'ASSISTANT MANAGER',
            4 => 'KARU/KASI',
            5 => 'GROUP LEADER',
            6 => 'JUNIOR MANAGER',
            7 => 'SENIOR GENERAL MANAGER',
            8 => 'STAFF',
            9 => 'ADMIN',
            10 => 'GENERAL MANAGER',
        ];

        $buildings = [
            1 => 'B1',
            2 => 'B2',
            3 => 'MD',
            4 => 'Utility',
            5 => 'Quality',
            6 => 'Office 2F',
        ];

        $contracts = [
            2 => 'CONTRACT 1',
            3 => 'CONTRACT 2',
            4 => 'CONTRACT 3',
            5 => 'CONTRACT 4',
        ];

        foreach ($positions as $id => $name) {
            Position::updateOrCreate(['id' => $id], ['nama' => $name]);
        }

        foreach ($sections as $id => $name) {
            Section::updateOrCreate(['id' => $id], ['nama' => $name]);
        }

        foreach ($levels as $id => $name) {
            Level::updateOrCreate(['id' => $id], ['nama' => $name]);
        }

        foreach ($buildings as $id => $name) {
            Building::updateOrCreate(['id' => $id], ['nama' => $name]);
        }

        foreach ($contracts as $id => $name) {
            Contract::updateOrCreate(['id' => $id], ['name' => $name]);
        }
    }

    private function seedLeaves(): void
    {
        $leaves = [
            1 => ['nama' => 'National Collective Leave', 'badge' => 'bg-soft-warning border-warning'],
            2 => ['nama' => 'National holiday', 'badge' => 'bg-soft-danger border-danger'],
            3 => ['nama' => 'Company Collective Leave', 'badge' => 'bg-soft-info border-info'],
            4 => ['nama' => 'Company Event', 'badge' => 'bg-soft-success border-success'],
        ];

        foreach ($leaves as $id => $data) {
            Leave::updateOrCreate(['id' => $id], $data);
        }
    }

    private function seedUsersAndEmployees(): void
    {
        $defaultEmployeeData = [
            'area_id' => 1,
            'department_id' => 1,
            'section_id' => 1,
            'position_id' => 9,
            'level_id' => 9,
            'building_id' => 1,
            'status' => 'ACTIVE',
            'work_location' => 'HEADQUARTERS / FACTORY',
        ];

        $demoUser = User::updateOrCreate(
            ['email' => 'admin@demo.local'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'status' => 1,
            ]
        );

        $demoEmployee = Employee::updateOrCreate(
            ['email' => 'admin@demo.local'],
            array_merge($defaultEmployeeData, [
                'nik' => 'DEMO-EMP-001',
                'fullname' => 'Demo Admin',
                'email' => 'admin@demo.local',
                'hp' => '081234567890',
                'gender' => 'Male',
                'religion' => 'Islam',
                'marital' => 'Single',
                'birthplace' => 'Sidoarjo',
                'birthdate' => '1990-01-01',
                'joindate' => '2024-01-02',
                'avatar' => null,
            ])
        );

        $demoUser->employee_id = $demoEmployee->id;
        $demoUser->save();

        User::orderBy('id')->get()->each(function (User $user) use ($defaultEmployeeData) {
            $employee = $user->employee;

            if (!$employee) {
                $employee = Employee::create(array_merge($defaultEmployeeData, [
                    'nik' => 'DEMO-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                    'fullname' => $user->name,
                    'email' => $user->email,
                    'hp' => null,
                    'gender' => 'Male',
                    'religion' => 'Islam',
                    'marital' => 'Single',
                    'birthplace' => 'Demo City',
                    'birthdate' => '1990-01-01',
                    'joindate' => now()->toDateString(),
                    'avatar' => null,
                ]));

                $user->employee_id = $employee->id;
                $user->save();
                return;
            }

            $employee->fill(array_merge($defaultEmployeeData, [
                'fullname' => $employee->fullname ?: $user->name,
                'email' => $employee->email ?: $user->email,
                'nik' => $employee->nik ?: 'DEMO-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                'hp' => $employee->hp ?: null,
                'gender' => $employee->gender ?: 'Male',
                'religion' => $employee->religion ?: 'Islam',
                'marital' => $employee->marital ?: 'Single',
                'birthplace' => $employee->birthplace ?: 'Demo City',
                'birthdate' => $employee->birthdate ?: '1990-01-01',
                'joindate' => $employee->joindate ?: now()->toDateString(),
                'avatar' => $employee->avatar ?: null,
            ]));
            $employee->save();
        });
    }

    private function seedAbout(): void
    {
        $user = User::whereNotNull('employee_id')->orderBy('id')->first();

        if (!$user) {
            return;
        }

        About::updateOrCreate(
            ['version' => 'demo-1.0.0'],
            [
                'user_id' => $user->id,
                'release_date' => now()->toDateString(),
                'description' => 'Dummy release data for local dashboard testing.',
            ]
        );
    }

    private function seedCalendar(): void
    {
        $year = now()->year;
        $fileName = $year . '.pdf';
        $sourcePdf = public_path('assets/flip/pdf/pdf.pdf');

        if (is_file($sourcePdf)) {
            Storage::disk('public')->put('calendar/' . $fileName, file_get_contents($sourcePdf));
        }

        $tempCalendar = Tempcalendar::updateOrCreate(
            ['tahun' => (string) $year],
            ['file_calendar' => $fileName]
        );

        $events = [
            [
                'id_leave' => 2,
                'event' => 'New Year',
                'type' => 1,
                'tanggal_awal' => $year . '-01-01',
                'tanggal_akhir' => null,
            ],
            [
                'id_leave' => 1,
                'event' => 'Company Collective Leave',
                'type' => 1,
                'tanggal_awal' => $year . '-04-21',
                'tanggal_akhir' => null,
            ],
            [
                'id_leave' => 3,
                'event' => 'Company Event',
                'type' => 2,
                'tanggal_awal' => $year . '-08-18',
                'tanggal_akhir' => null,
            ],
        ];

        foreach ($events as $event) {
            Calendar::updateOrCreate(
                [
                    'id_temp_calendar' => $tempCalendar->id,
                    'event' => $event['event'],
                    'type' => $event['type'],
                    'tanggal_awal' => $event['tanggal_awal'],
                ],
                $event
            );
        }
    }

    private function seedLineApproval(): void
    {
        $approver = Employee::whereNotNull('department_id')->orderBy('id')->first();

        if (!$approver) {
            return;
        }

        LineApproval::updateOrCreate(
            [
                'approval_type' => 'DEMO',
                'group_name' => 'DEFAULT',
            ],
            [
                'department_id' => $approver->department_id,
                'area_id' => $approver->area_id,
                'building_id' => $approver->building_id,
                'position_id' => $approver->position_id,
                'section_id' => $approver->section_id,
                'approve_1' => $approver->id,
                'drafter' => $approver->id,
            ]
        );
    }
}