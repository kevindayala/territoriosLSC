<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Plantilla de Importación: Territorios + Personas (Sordos).
 *
 * Genera un archivo Excel vacío con las cabeceras correctas y una fila de ejemplo
 * para que el usuario pueda llenar los datos y luego importarlos.
 *
 * La plantilla incluye una columna adicional "Localidad (Opcional)" que permite
 * definir jerarquías de ciudades (ej: Ciudad = "Santander", Localidad = "Florida").
 *
 * @see \App\Imports\TerritoriesImport  Clase que procesa este formato
 */
class TerritoriesTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * Fila de ejemplo para guiar al usuario.
     *
     * Esta fila muestra el formato esperado para cada campo.
     * El usuario puede eliminar esta fila y agregar sus propios datos.
     */
    public function array(): array
    {
        return [
            ['TERR-001', 'Bucaramanga', 'Norte', 'Centro', 'Activo', 'Notas de territorio', 'Juan Perez', 'Calle Falsa 123', 'https://maps.google.com/?q=...', 'Sordo usa LSC', 'Activo'],
        ];
    }

    /**
     * Cabeceras del archivo Excel.
     *
     * Nota: El formato de "Nombre de Ciudad" acepta dos formatos:
     * 1. Solo nombre: "Bucaramanga"
     * 2. Con jerarquía: "Santander > Florida" (padre > hijo)
     *
     * Si se usa el campo "Localidad (Opcional)", la ciudad se convierte en padre
     * y la localidad en hija. Ejemplo: Ciudad = "Santander", Localidad = "Florida".
     */
    public function headings(): array
    {
        return [
            'Codigo (Territorio)',
            'Nombre de Ciudad',
            'Localidad (Opcional)',
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
