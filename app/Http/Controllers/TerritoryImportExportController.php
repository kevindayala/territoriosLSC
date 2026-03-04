<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TerritoriesTemplateExport;
use App\Imports\TerritoriesImport;
use Maatwebsite\Excel\Facades\Excel;

class TerritoryImportExportController extends Controller
{
    public function exportTemplate()
    {
        return Excel::download(new TerritoriesTemplateExport, 'plantilla_territorios.xlsx');
    }

    public function exportBackup()
    {
        return Excel::download(new \App\Exports\TerritoriesBackupExport, 'backup_territorios_' . date('Y_m_d_His') . '.xlsx');
    }

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
}
