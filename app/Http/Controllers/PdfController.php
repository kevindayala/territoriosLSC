<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Territory;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function exportAssignments($year)
    {
        // Get territories with assignments for the specific year
        $territories = Territory::with([
            'assignments' => function ($q) use ($year) {
                $q->whereYear('assigned_at', $year)
                    ->orderBy('assigned_at', 'asc');
            },
        ])->orderBy('code')->get();

        $pdf = Pdf::loadView('pdf.assignments', compact('territories', 'year'));

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("asignaciones_{$year}.pdf");
    }
}
