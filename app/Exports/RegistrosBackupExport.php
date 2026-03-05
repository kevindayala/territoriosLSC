<?php

namespace App\Exports;

use App\Models\TerritoryAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrosBackupExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $registros = TerritoryAssignment::with(['territory.city', 'assignedTo', 'assignedBy'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        $rows = [];

        foreach ($registros as $registro) {
            $rows[] = [
                'codigo_territorio' => $registro->territory->code ?? '',
                'ciudad' => $registro->territory->city->name ?? '',
                'barrio' => $registro->territory->neighborhood_name ?? '',
                'asignado_a' => $registro->assignedTo->name ?? '',
                'asignado_por' => $registro->assignedBy->name ?? '',
                'tipo' => $registro->type === 'personal' ? 'Personal' : 'Regular',
                'fecha_asignacion' => $registro->assigned_at ? $registro->assigned_at->format('d/m/Y') : '',
                'fecha_completado' => $registro->completed_at ? $registro->completed_at->format('d/m/Y') : '',
                'fecha_devolucion' => $registro->due_date ? $registro->due_date->format('d/m/Y') : '',
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Código Territorio',
            'Ciudad',
            'Barrio',
            'Asignado A',
            'Asignado Por',
            'Tipo',
            'Fecha Asignación',
            'Fecha Completado',
            'Fecha Devolución',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
