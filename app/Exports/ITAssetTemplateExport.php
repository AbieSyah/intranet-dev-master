<?php

namespace App\Exports;

use App\Models\AssetType;
use App\Models\ITAsset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ITAssetTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Import' => new MainTemplateSheet(),
            'Data Referensi' => new ReferenceSheet(),
        ];
    }
}

class MainTemplateSheet implements WithTitle, WithHeadings, WithEvents
{
    public function title(): string
    {
        return 'Template Import';
    }
    
    /**
     * Decide header column
     */
    public function headings(): array
    {
        return [
            'Asset Code (ITyymm-XXXX)',
            'Asset Type',
            'Brand',
            'Hardware Specification',
            'Software Specification',
            'Year Registered (YYYY-MM-DD)',
            'Price',
            'Status (Pilih Dropdown)',
            'NIK Employee (Optional, to assign asset to employee)',
        ];
    }

    /**
     * Manipulate sheet after created (for Dropdown)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $assetTypes = AssetType::get()->map(fn($type) => $type->id . '-' . $type->name)->toArray();
                // $typeList = '"' . implode(',', $assetTypes) . '"';
                
                // $this->applyDropdown($sheet, 'B2:B100', $typeList, 'Pilih Tipe Aset');

                $statusList = [
                    ITAsset::STATUS_ACTIVE,
                    ITAsset::STATUS_BACKUP,
                    // ITAsset::STATUS_BROKEN,
                    // ITAsset::STATUS_DISPOSED,
                ];
                $statusList = "\"".implode(',', $statusList)."\"";
                $this->applyDropdown($sheet, 'H2:H100', $statusList, 'Pilih Status');
            },
        ];
    }

    /**
     * Helper function to implement dropdown validations
     */
    private function applyDropdown($sheet, $range, $formula, $title)
    {
        $validation = $sheet->getCell(explode(':', $range)[0])->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input Salah');
        $validation->setError('Mohon pilih data yang ada dalam daftar.');
        $validation->setPromptTitle($title);
        $validation->setFormula1($formula);

        $sheet->setDataValidation($range, $validation);
    }
}

class ReferenceSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        // Mengambil hanya nama AssetType dalam satu kolom
        return AssetType::pluck('name')->map(fn($name) => [$name]);
    }

    public function title(): string
    {
        return 'AssetTypeLists';
    }
}