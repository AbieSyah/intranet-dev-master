<?php

namespace App\Exports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationProcessExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $formStatus;
    public function __construct($formStatus)
    {
        $this->formStatus = $formStatus;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Evaluation::with(['employee', 'appraisal.department'])
                    ->where('evaluations.status', '!=', 'DONE');
        if ($this->formStatus !== 'ALL') {
            $query->where('evaluations.status', $this->formStatus);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'NO',
            'NAME',
            'DEPARTMENT',
            'SECTION',
            'POSITION',
            'ORGANIZATION',
            'START DATE',
            'END DATE',
            'PURPOSE',
            'SCORE',
            'GRADE',
            'DECISION',
            'CONTRACT EXTEND DURATION',
            'DECISION REASON',
        ];
    }

    public function map($evaluation): array
    {
        return [
            $evaluation->employee->nik ?? '-',
            $evaluation->release_id ?? '-',
            $evaluation->employee->fullname ?? '-',
            $evaluation->employee->department->name ?? '-',
            $evaluation->employee->section->nama ?? '-',
            $evaluation->employee->position->nama ?? '-',
            $evaluation->employee->building->nama ?? '-',
            $evaluation->eval_start?->format('Y-m-d') ?? '-',
            $evaluation->eval_end?->format('Y-m-d') ?? '-',
            $evaluation->purpose ?? '-',
            $evaluation->total_score ?? '-',
            $evaluation->grade ?? '-',
            $evaluation->decision_employment ?? '-',
            $evaluation->month_extend ? "{$evaluation->month_extend} months" : '-',
            $evaluation->decision_reason ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styleHeader = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($styleHeader);
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $excludedColumns = ['C'];
        foreach ($excludedColumns as $col) {
            $sheet->getStyle($col . '1')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            if (in_array($colLetter, $excludedColumns)) {
                continue;
            }
            $headerAlignment = $sheet->getStyle($colLetter . '1')
                ->getAlignment()
                ->getHorizontal();
            $sheet->getStyle($colLetter . '2:' . $colLetter . $highestRow)
                ->getAlignment()
                ->setHorizontal($headerAlignment);
        }
        // Column Selected
        $sheet->setSelectedCells('C2');
    }
}
