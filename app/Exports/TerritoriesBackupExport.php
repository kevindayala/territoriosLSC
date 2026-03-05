<?php

namespace App\Exports;

use App\Models\Territory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exportación de Backup: Territorios + Personas (Sordos).
 *
 * Genera un archivo Excel con todos los territorios y sus personas asociadas.
 * Cada fila del Excel representa una persona dentro de un territorio.
 * Si un territorio no tiene personas, se genera una fila solo con los datos del territorio.
 *
 * Estructura del Excel generado:
 * | Codigo | Ciudad         | Barrio | Estado | Notas | Sordo Nombre | Sordo Dirección | ... |
 * |--------|----------------|--------|--------|-------|--------------|-----------------|-----|
 * | B1     | Bucaramanga    | Norte  | Activo |       | Juan Perez   | Calle 1         | ... |
 * | B1     | Bucaramanga    | Norte  | Activo |       | Maria Lopez  | Calle 2         | ... |
 * | F1     | Florida        | Centro | Activo |       |              |                 |     |
 *
 * Las cabeceras coinciden con el formato esperado por TerritoriesImport
 * para que un backup exportado pueda ser re-importado directamente.
 *
 * @see \App\Imports\TerritoriesImport  Clase que importa este mismo formato
 * @see \App\Exports\TerritoriesTemplateExport  Plantilla vacía con fila de ejemplo
 */
class TerritoriesBackupExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * Obtener todos los territorios con sus personas para exportar.
     *
     * Los territorios se ordenan por ciudad y luego por código para facilitar
     * la lectura del archivo. Las ciudades con jerarquía padre > hijo se
     * muestran como "Padre > Hijo" (ej: "Santander > Florida").
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $territories = Territory::with(['city', 'persons'])->orderBy('city_id')->orderBy('code')->get();
        $rows = [];

        foreach ($territories as $territory) {
            // Traducir el estado del territorio al español
            $territoryStatus = 'Activo';
            if ($territory->status === 'inactive')
                $territoryStatus = 'Inactivo';

            // Construir el nombre de la ciudad con su padre si existe
            // Ejemplo: "Santander > Florida" o simplemente "Bucaramanga"
            $cityName = '';
            if ($territory->city) {
                $cityName = $territory->city->parent
                    ? $territory->city->parent->name . ' > ' . $territory->city->name
                    : $territory->city->name;
            }

            if ($territory->persons->isEmpty()) {
                // Territorio sin personas: generar una fila con campos de persona vacíos
                $rows[] = [
                    'codigo' => $territory->code,
                    'ciudad' => $cityName,
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
                // Territorio con personas: generar una fila por cada persona
                foreach ($territory->persons as $person) {
                    // Traducir el estado de la persona al español
                    $personStatus = 'Activo';
                    if ($person->status === 'pending')
                        $personStatus = 'Pendiente';
                    if ($person->status === 'inactive')
                        $personStatus = 'Inactivo';

                    $rows[] = [
                        'codigo' => $territory->code,
                        'ciudad' => $cityName,
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

    /**
     * Cabeceras del archivo Excel.
     *
     * Estas cabeceras deben coincidir con las que espera TerritoriesImport
     * para que el archivo de backup sea compatible con la importación.
     */
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
