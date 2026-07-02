<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    protected $formStatus;

    public function __construct($formStatus)
    {
        $this->formStatus = $formStatus;
    }

    public function collection()
    {
        $query = Employee::with([
            'department', 'area', 'position', 'level', 'building', 'contract'
        ]);
        if (!empty($this->formStatus) && $this->formStatus !== 'ALL') {
            $statuses = explode(',', $this->formStatus);
            $query->whereIn('status', $statuses);
        } else {
            $query->where('status', '!=', 'TERMINATED');
        }
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'NIK',                           
            'NO_KTP',                        
            'FULLNAME',                      
            'EMAIL',                         
            'ADDRESS_KTP',                   
            'DOMICILE_ADDRESS',              
            'BIRTHPLACE',                    
            'BIRTHDATE',                     
            'GENDER',                        
            'BLOOD_TYPE',                    
            'RELIGION',                      
            'MARITAL',                       
            'HP',                            
            'JOIN_DATE',                     
            'END_DATE',                      
            'STATUS',                        
            'REASON',
            'AREA',                          
            'DEPARTMENT',                    
            'SECTION',                       
            'POSITION',                      
            'LEVEL',                         
            'ORGANIZATION',                  
            'CONTRACT_START_DATE',           
            'CONTRACT_SEQUENCE',             
            'LATEST_AGREEMENT_NO',           
            'ACTIVE_AGREEMENT_NO',           
            'WORK_LOCATION',                 
            'PERMANENT_STARTDATE',           
            'ISO_POSITION',                  
            'COST_CENTER',                   
            'EMERGENCY_CONTACT',             
            'EMERGENCY_CONTACT_RELATION',    
            'EMERGENCY_CONTACT_HANDPHONE',   
            'EMERGENCY_CONTACT_ADDRESS',     
            'LAST_EDUCATION',                
            'MAJOR_LAST_EDUCATION',          
            'LAST_EDUCATION_INSTITUTIONAL',  
            'PTKP_STATUS',                   
            'NPWP',                          
            'BPJS_KESEHATAN',                
            'BPJS_KETENAGAKERJAAN',          
            'BANK_NAME',                     
            'BANK_ACCOUNT',                  
            'BANK_ACCOUNT_HOLDER',           
            'OUTSOURCING_VENDOR',
            'SERVICE_YEAR',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();
        $textColumns = [
            'A',  // NIK
            'B',  // NO_KTP
            'M',  // HP
            'AE', // COST_CENTER
            'AH', // EMERGENCY_CONTACT_HANDPHONE
            'AN', // NPWP
            'AO', // BPJS_KESEHATAN
            'AP', // BPJS_KETENAGAKERJAAN
            'AR'  // BANK_ACCOUNT
        ];
        if (in_array($column, $textColumns)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function map($employee): array
    {
        return [
            $employee->nik,                                     
            $employee->no_ktp,                                  
            $employee->fullname,                                
            $employee->email,                                   
            $employee->addressktp,                              
            $employee->domicile_address,                        
            $employee->birthplace,                              
            $employee->birthdate,                               
            $employee->gender,                                  
            $employee->blood_type,                              
            $employee->religion,                                
            $employee->marital,                                 
            $employee->hp,                                      
            $employee->joindate,                                
            $employee->enddate,                                 
            $employee->status,                                  
            $employee->reason,
            $employee->area->name ?? null,                      
            $employee->department->name ?? null,                
            $employee->section->nama ?? null,                   
            $employee->position->nama ?? null,                  
            $employee->level->nama ?? null,                     
            $employee->building->nama ?? null,                  
            $employee->contract_startdate,                      
            $employee->contract->name ?? ($employee->contract_number ?? null), 
            $employee->latest_agreement_number,                 
            $employee->active_agreement_number,                 
            $employee->work_location,                           
            $employee->permanent_startdate,                     
            $employee->iso_position,                            
            $employee->cost_center,                             
            $employee->emergency_contact,                       
            $employee->emergency_contact_relation,              
            $employee->emergency_contact_handphone,             
            $employee->emergency_contact_address,               
            $employee->last_education,                          
            $employee->major_last_education,                    
            $employee->last_education_institutional,            
            $employee->tax_dependents,                          
            $employee->npwp,                                    
            $employee->bpjs_kesehatan,                          
            $employee->bpjs_ketenagakerjaan,                    
            $employee->bank_name,
            $employee->bank_account,
            $employee->bank_account_holder,
            $employee->outsourcing_vendor,
            $employee->serviceYears,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => ['bold' => true],
        ]);
        $leftAlignColumns = ['C', 'E', 'F'];
        foreach ($leftAlignColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        $textFormatColumns = ['A', 'B', 'M', 'Z', 'AA', 'AE', 'AH', 'AN', 'AO', 'AP', 'AR'];
        foreach ($textFormatColumns as $col) {
            $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }
        $sheet->setSelectedCells('C2');
    }
}