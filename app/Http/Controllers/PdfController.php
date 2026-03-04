<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Territory;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function exportAssignments(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start_date = $request->start_date . ' 00:00:00';
        $end_date = $request->end_date . ' 23:59:59';

        $territories = Territory::with([
            'assignments' => function ($q) use ($start_date, $end_date) {
                $q->whereBetween('assigned_at', [$start_date, $end_date])
                    ->orderBy('assigned_at', 'asc');
            },
        ])->get()->sortBy('code', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $pdf = Pdf::loadView('pdf.assignments', compact('territories', 'start_date', 'end_date'));

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("asignaciones_" . explode(' ', $start_date)[0] . "_al_" . explode(' ', $end_date)[0] . ".pdf");
    }
}
