<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TerritoriesTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            // Fila de ejemplo opcional (Usa Ciudad > Subcategoría para sub-zonas, ej: Bucaramanga > Norte)
            ['TERR-001', 'Bucaramanga > Norte', 'Centro', 'Activo', 'Notas de territorio', 'Juan Perez', 'Calle Falsa 123', 'https://maps.google.com/?q=...', 'Sordo usa LSC', 'Activo'],
        ];
    }

    public function headings(): array
    {
        return [
            'Codigo (Territorio)',
            'Nombre de Ciudad',
            'Nombre de Barrio',
            'Estado Territorio',
            'Notas Territorio',
            'Sordo Nombre',
            'Sordo Dirección',
            'Sordo Mapa URL',
            'Sordo Notas',
            'Sordo Estado'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
