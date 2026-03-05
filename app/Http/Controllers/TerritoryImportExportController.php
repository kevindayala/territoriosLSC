<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TerritoriesTemplateExport;
use App\Imports\TerritoriesImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controlador para Exportar e Importar datos de Territorios y Registros.
 *
 * Maneja dos tipos de backup:
 *
 * 1. TERRITORIOS (admin/territories):
 *    - Exporta/Importa los territorios con sus personas (sordos).
 *    - Útil para respaldar la estructura de territorios y los datos de personas.
 *
 * 2. REGISTROS (admin/registros):
 *    - Exporta/Importa las asignaciones de territorios (quién trabajó qué territorio y cuándo).
 *    - Al importar, actualiza automáticamente las fechas de los territorios (última vez realizado).
 *
 * Rutas relacionadas (definidas en routes/web.php):
 *   GET  /admin/territories/export-template  → exportTemplate()
 *   GET  /admin/territories/export-backup    → exportBackup()
 *   POST /admin/territories/import           → import()
 *   GET  /admin/registros/export-backup      → exportRegistrosBackup()
 *   POST /admin/registros/import             → importRegistros()
 *
 * @see \App\Exports\TerritoriesBackupExport   Exportación de territorios + sordos
 * @see \App\Exports\TerritoriesTemplateExport  Plantilla vacía para importar territorios
 * @see \App\Imports\TerritoriesImport          Importación de territorios + sordos
 * @see \App\Exports\RegistrosBackupExport      Exportación de registros de asignaciones
 * @see \App\Imports\RegistrosImport            Importación de registros de asignaciones
 */
class TerritoryImportExportController extends Controller
{
    /**
     * Descargar una plantilla Excel vacía con las columnas necesarias
     * para importar territorios y personas (sordos).
     *
     * La plantilla incluye una fila de ejemplo como guía.
     */
    public function exportTemplate()
    {
        return Excel::download(new TerritoriesTemplateExport, 'plantilla_territorios.xlsx');
    }

    /**
     * Exportar todos los territorios con sus personas (sordos) a un archivo Excel.
     *
     * El archivo incluye: código de territorio, ciudad, barrio, estado,
     * notas, y los datos de cada persona (nombre, dirección, mapa, notas, estado).
     *
     * El nombre del archivo incluye la fecha y hora para identificar el backup.
     */
    public function exportBackup()
    {
        return Excel::download(new \App\Exports\TerritoriesBackupExport, 'backup_territorios_' . date('Y_m_d_His') . '.xlsx');
    }

    /**
     * Importar territorios y personas (sordos) desde un archivo Excel.
     *
     * Comportamiento:
     * - Si el territorio ya existe (mismo código), se actualiza.
     * - Si no existe, se crea uno nuevo junto con su ciudad.
     * - Las personas se buscan por nombre + territorio. Si existen, se actualizan.
     *
     * Formatos aceptados: .xlsx, .csv, .xls (máximo 5MB).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:5120', // Max 5MB
        ]);

        try {
            Excel::import(new TerritoriesImport, $request->file('file'));
            return back()->with('success', 'Territorios y personas (sordos) importados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage() . ' (Línea ' . $e->getLine() . ')');
        }
    }

    /**
     * Exportar todos los registros de asignaciones de territorios a un archivo Excel.
     *
     * El archivo incluye: código de territorio, ciudad, barrio, usuario asignado,
     * usuario que asignó, tipo (regular/personal), y fechas (asignación, completado, devolución).
     *
     * El nombre del archivo incluye la fecha y hora para identificar el backup.
     */
    public function exportRegistrosBackup()
    {
        return Excel::download(new \App\Exports\RegistrosBackupExport, 'backup_registros_' . date('Y_m_d_His') . '.xlsx');
    }

    /**
     * Importar registros de asignaciones de territorios desde un archivo Excel.
     *
     * Comportamiento:
     * - Busca el territorio por código. Si no existe, omite la fila.
     * - Busca el usuario por nombre. Si no existe, omite la fila (sin error).
     * - Si la asignación ya existe (mismo territorio + usuario + fecha), se actualiza.
     * - Si no existe, se crea una nueva asignación.
     * - Al finalizar, actualiza automáticamente el campo `last_completed_at`
     *   de cada territorio afectado para reflejar la fecha más reciente de completado.
     *
     * Formatos aceptados: .xlsx, .csv, .xls (máximo 5MB).
     *
     * @return \Illuminate\Http\RedirectResponse Con mensaje detallado de resultados.
     */
    public function importRegistros(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\RegistrosImport;
            Excel::import($import, $request->file('file'));

            // Actualizar last_completed_at de todos los territorios procesados
            $import->updateTerritoryDates();

            return back()->with('success', $import->getSummaryMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar registros: ' . $e->getMessage());
        }
    }
}
