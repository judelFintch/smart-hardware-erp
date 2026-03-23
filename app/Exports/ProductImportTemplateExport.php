<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductImportTemplateExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'sku',
            'name',
            'barcode',
            'unit_code',
            'description',
            'margin',
            'reorder_level',
        ];
    }

    public function collection(): Collection
    {
        return collect([
            [
                'SKU-001',
                'Perceuse sans fil 18V',
                '1234567890123',
                'pcs',
                'Perceuse portable avec batterie et chargeur',
                25,
                5,
            ],
        ]);
    }
}
