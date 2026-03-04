<?php

namespace App\Exports;

use App\Models\Territory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TerritoriesBackupExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        // Recuperamos todos los territorios con sus ciudades y personas (incluso soft-deleted si quisieramos, pero por ahora solo activos)
        $territories = Territory::with(['city', 'persons'])->orderBy('city_id')->orderBy('code')->get();
        $rows = [];

        foreach ($territories as $territory) {
            $territoryStatus = 'Activo';
            if ($territory->status === 'inactive')
                $territoryStatus = 'Inactivo';

            if ($territory->persons->isEmpty()) {
                // Fila con solo los datos del territorio
                $rows[] = [
                    'codigo' => $territory->code,
                    'ciudad' => $territory->city ? ($territory->city->parent ? $territory->city->parent->name . ' > ' . $territory->city->name : $territory->city->name) : '',
                    'barrio' => $territory->neighborhood_name,
                    'estado_territorio' => $territoryStatus,
                    'notas_territorio' => $territory->notes,
                    'sordo_nombre' => '',
                    'sordo_direccion' => '',
                    'sordo_map_url' => '',
                    'sordo_notas' => '',
                    'sordo_estado' => ''
                ];
            } else {
                // Iteramos cada persona en el territorio
                foreach ($territory->persons as $person) {
                    $personStatus = 'Activo';
                    if ($person->status === 'pending')
                        $personStatus = 'Pendiente';
                    if ($person->status === 'inactive')
                        $personStatus = 'Inactivo';

                    $rows[] = [
                        'codigo' => $territory->code,
                        'ciudad' => $territory->city ? ($territory->city->parent ? $territory->city->parent->name . ' > ' . $territory->city->name : $territory->city->name) : '',
                        'barrio' => $territory->neighborhood_name,
                        'estado_territorio' => $territoryStatus,
                        'notas_territorio' => $territory->notes,
                        'sordo_nombre' => $person->full_name,
                        'sordo_direccion' => $person->address,
                        'sordo_map_url' => $person->map_url,
                        'sordo_notas' => $person->notes,
                        'sordo_estado' => $personStatus
                    ];
                }
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        // Las cabeceras coinciden con la plantilla de importación
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
