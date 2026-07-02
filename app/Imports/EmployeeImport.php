<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Area;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use App\Models\Level;
use App\Models\Master\Building;
use App\Models\Master\Contract;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, WithMapping, WithBatchInserts, WithStartRow, SkipsEmptyRows
{
    public $importedCount = 0;
    protected static $masterData = null;

    public function headingRow(): int { return 1; }
    public function startRow(): int { return 2; }
    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 1000; }

    public function map($row): array
    {
        if (empty(trim($row['nik'] ?? ''))) return [];

        // 1. Convert Excel Serial Date to PHP Object
        $dateFields = ['join_date', 'birthdate', 'end_date', 'contract_start_date', 'permanent_startdate'];
        foreach ($dateFields as $field) {
            if (isset($row[$field]) && is_numeric($row[$field])) {
                $row[$field] = Date::excelToDateTimeObject($row[$field]);
            }
        }

        // 2. Clean Numeric Strings
        $numericStrings = ['no_ktp', 'npwp', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'bank_account', 'cost_center'];
        foreach ($numericStrings as $field) {
            if (isset($row[$field])) {
                $row[$field] = preg_replace('/\D/', '', (string) $row[$field]);
            }
        }

        // 3. String Formatting
        if (isset($row['fullname'])) {
            $row['fullname'] = strtoupper(str_replace(['’', '`', '´', '‘'], "'", trim($row['fullname'])));
        }
        if (isset($row['gender'])) $row['gender'] = ucfirst(strtolower(trim($row['gender'])));
        if (isset($row['status'])) $row['status'] = strtoupper(trim($row['status']));
        if (isset($row['blood_type'])) $row['blood_type'] = strtoupper(trim($row['blood_type']));
        return $row;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'fullname' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'status' => 'required|in:PERMANENT,PROBATION,CONTRACT,TERMINATED,OUTSOURCING',
            'ptkp_status' => ['nullable', Rule::in(Employee::getTaxDependentsOptions())],
        ];
    }

    public function collection(Collection $rows)
    {
        if (self::$masterData === null) {
            self::$masterData = [
                'areas' => Area::get()->keyBy(fn($i) => strtoupper(trim($i->name)))->map->id,
                'depts' => Department::get()->keyBy(fn($i) => strtoupper(trim($i->name)))->map->id,
                'sects' => Section::get()->keyBy(fn($i) => strtoupper(trim($i->nama)))->map->id,
                'pos'   => Position::get()->keyBy(fn($i) => strtoupper(trim($i->nama)))->map->id,
                'lvl'   => Level::get()->keyBy(fn($i) => strtoupper(trim($i->nama)))->map->id,
                'build' => Building::get()->keyBy(fn($i) => strtoupper(trim($i->nama)))->map->id,
                'contr' => Contract::get()->keyBy(fn($i) => strtoupper(trim($i->name)))->map->id,
            ];
        }
        foreach ($rows as $row) {
            $this->importedCount++;
            $area_id  = isset($row['area']) ? (self::$masterData['areas'][strtoupper(trim($row['area']))] ?? null) : null;
            $dept_id  = isset($row['department']) ? (self::$masterData['depts'][strtoupper(trim($row['department']))] ?? null) : null;
            $sect_id  = isset($row['section']) ? (self::$masterData['sects'][strtoupper(trim($row['section']))] ?? null) : null;
            $pos_id   = isset($row['position']) ? (self::$masterData['pos'][strtoupper(trim($row['position']))] ?? null) : null;
            $lvl_id   = isset($row['level']) ? (self::$masterData['lvl'][strtoupper(trim($row['level']))] ?? null) : null;
            $build_id = isset($row['organization']) ? (self::$masterData['build'][strtoupper(trim($row['organization']))] ?? null) : null;
            $contr_id = isset($row['contract_sequence']) ? (self::$masterData['contr'][strtoupper(trim($row['contract_sequence']))] ?? null) : null;
            Employee::updateOrCreate(
                ['nik' => $row['nik']],
                [
                    'no_ktp' => $row['no_ktp'],
                    'fullname' => $row['fullname'],
                    'email' => $row['email'],
                    'addressktp' => $row['address_ktp'],
                    'domicile_address' => $row['domicile_address'] ?? null,
                    'birthplace' => $row['birthplace'] ?? null,
                    'birthdate' => $row['birthdate'],
                    'gender' => $row['gender'],
                    'blood_type' => $row['blood_type'] ?? null,
                    'religion' => $row['religion'],
                    'marital' => $row['marital'] ?? null,
                    'hp' => $row['hp'],
                    'joindate' => $row['join_date'],
                    'enddate' => $row['end_date'],
                    'status' => $row['status'],
                    'reason' => $row['reason'] ?? null,
                    'area_id' => $area_id,
                    'department_id' => $dept_id,
                    'section_id' => $sect_id,
                    'position_id' => $pos_id,
                    'level_id' => $lvl_id,
                    'building_id' => $build_id,
                    'contract_number' => $contr_id,
                    'contract_startdate' => $row['contract_start_date'],
                    'latest_agreement_number' => $row['latest_agreement_no'] ?? null,
                    'active_agreement_number' => $row['active_agreement_no'] ?? null,
                    'work_location' => $row['work_location'] ?? null,
                    'permanent_startdate' => $row['permanent_startdate'],
                    'iso_position' => $row['iso_position'] ?? null,
                    'cost_center' => $row['cost_center'],
                    'emergency_contact' => $row['emergency_contact'] ?? null,
                    'emergency_contact_relation' => $row['emergency_contact_relation'] ?? null,
                    'emergency_contact_handphone' => $row['emergency_contact_handphone'] ?? null,
                    'emergency_contact_address' => $row['emergency_contact_address'] ?? null,
                    'last_education' => $row['last_education'] ?? null,
                    'major_last_education' => $row['major_last_education'] ?? null,
                    'last_education_institutional' => $row['last_education_institutional'] ?? null,
                    'tax_dependents' => $row['ptkp_status'] ?? null,
                    'npwp' => $row['npwp'] ?? null,
                    'bpjs_kesehatan' => $row['bpjs_kesehatan'] ?? null,
                    'bpjs_ketenagakerjaan' => $row['bpjs_ketenagakerjaan'] ?? null,
                    'bank_name' => $row['bank_name'] ?? null,
                    'bank_account' => $row['bank_account'] ?? null,
                    'bank_account_holder' => $row['bank_account_holder'] ?? null,
                    'outsourcing_vendor' => $row['outsourcing_vendor'] ?? null,
                ]
            );
        }
    }
}