<?php

namespace App\Exports;

use App\Models\TerritoryAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exportación de Backup: Registros de Asignaciones de Territorios.
 *
 * Genera un archivo Excel con todas las asignaciones de territorios (tabla territory_assignments).
 * Cada fila representa una asignación: quién trabajó qué territorio, cuándo y su estado.
 *
 * Estructura del Excel generado:
 * | Código Territorio | Ciudad      | Barrio | Asignado A   | Asignado Por | Tipo    | Fecha Asignación | Fecha Completado | Fecha Devolución |
 * |-------------------|-------------|--------|--------------|--------------|---------|------------------|------------------|------------------|
 * | B1                | Bucaramanga | Norte  | Juan Perez   | Admin        | Regular | 01/03/2026       | 15/03/2026       |                  |
 * | F1                | Florida     | Centro | Maria Lopez  | Admin        | Personal| 05/02/2026       |                  | 05/04/2026       |
 *
 * IMPORTANTE: Los usuarios se exportan por NOMBRE, no por ID.
 * Al importar, se buscan por nombre exacto en la tabla users.
 * Si el nombre no coincide con ningún usuario existente, esa fila se omite.
 *
 * Las fechas se exportan en formato "d/m/Y" (ej: 01/03/2026).
 *
 * @see \App\Imports\RegistrosImport  Clase que importa este mismo formato
 */
class RegistrosBackupExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * Obtener todos los registros de asignaciones para exportar.
     *
     * Se cargan las relaciones necesarias (territory.city, assignedTo, assignedBy)
     * y se ordenan por fecha de asignación descendente (más recientes primero).
     *
     * @return \Illuminate\Support\Collection
     */
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
                'asignado_a' => $registro->assignedTo->name ?? '',     // Nombre del publicador
                'asignado_por' => $registro->assignedBy->name ?? '',     // Nombre de quien asignó
                'tipo' => $registro->type === 'personal' ? 'Personal' : 'Regular',
                'fecha_asignacion' => $registro->assigned_at ? $registro->assigned_at->format('d/m/Y') : '',
                'fecha_completado' => $registro->completed_at ? $registro->completed_at->format('d/m/Y') : '',
                'fecha_devolucion' => $registro->due_date ? $registro->due_date->format('d/m/Y') : '',
            ];
        }

        return collect($rows);
    }

    /**
     * Cabeceras del archivo Excel.
     *
     * Estas cabeceras deben coincidir con las que espera RegistrosImport
     * para que el archivo de backup sea compatible con la importación.
     * La librería Maatwebsite convierte las cabeceras a snake_case internamente
     * (ej: "Código Territorio" → "codigo_territorio").
     */
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

    /**
     * Estilos del archivo Excel.
     * La primera fila (cabeceras) se muestra en negrita.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
