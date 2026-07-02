<?php

namespace App\Exports;

use App\Models\Master\LineApprovalEmployee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LineApprovalEmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LineApprovalEmployee::with([
            'employee.position',
            'lineApproval.approve1',
            'lineApproval.approve2',
            'lineApproval.approve3',
            'lineApproval.approve4',
            'lineApproval.approve5',
            'lineApproval.approve6',
            'lineApproval.approve7',
            'lineApproval.approve8'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIK',
            'FULL NAME',
            'POSITION',
            'TYPE',
            'APPROVAL 1',
            'APPROVAL 2',
            'APPROVAL 3',
            'APPROVAL 4',
            'APPROVAL 5',
            'APPROVAL 6',
            'APPROVAL 7',
            'APPROVAL 8',
        ];
    }

    public function map($lineApprovalEmployee): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        return [
            $rowNumber,
            $lineApprovalEmployee->employee->nik ?? '-',
            $lineApprovalEmployee->employee->fullname ?? '-',
            $lineApprovalEmployee->employee->position->nama ?? '-',
            $lineApprovalEmployee->lineApproval->approval_type ?? '-',
            $lineApprovalEmployee->lineApproval->approve1->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve2->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve3->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve4->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve5->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve6->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve7->fullname ?? '-',
            $lineApprovalEmployee->lineApproval->approve8->fullname ?? '-',
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

        $styleColumns = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())->applyFromArray($styleColumns);
        $sheet->getStyle('B2:B' . $sheet->getHighestRow())->applyFromArray($styleColumns);
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->applyFromArray($styleColumns);
        $sheet->getStyle('E2:E' . $sheet->getHighestRow())->applyFromArray($styleColumns);

        // Column Selected
        $sheet->setSelectedCells('C2');
    }
}
