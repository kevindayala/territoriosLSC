<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>S-13 Registro de Asignación de Territorio</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #000; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; border: 3px solid black; }
        th, td { border: 1px solid black; padding: 3px; vertical-align: middle; }
        th { background-color: #d8d8d8; text-align: center; font-weight: normal; font-size: 11px; }
        .text-center { text-align: center; }
        .title { text-align: center; margin-bottom: 30px; font-size: 18px; font-weight: bold; font-family: Arial, sans-serif; }
        .year-section { margin-bottom: 5px; font-size: 15px; font-weight: bold; }
        .year-line { display: inline-block; width: 60px; border-bottom: 1px solid black; text-align: center; }
        .page-break { page-break-after: always; }
        
        /* Column widths */
        .col-terr { width: 7%; }
        .col-date { width: 11%; }
        /* The remaining 82% is for the 8 date columns, or 4 assignment columns */
    </style>
</head>
<body>
    <div class="title">
        REGISTRO DE ASIGNACIÓN DE TERRITORIO
    </div>
    
    <div class="year-section">
        Periodo: <span class="year-line" style="min-width: 360px; padding: 0 10px; white-space: nowrap;">{{ \Carbon\Carbon::parse($start_date)->locale('es_CO')->translatedFormat('j \d\e F \d\e Y') }} al {{ \Carbon\Carbon::parse($end_date)->locale('es_CO')->translatedFormat('j \d\e F \d\e Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-terr" rowspan="2">Núm.<br>de terr.</th>
                <th class="col-date" rowspan="2">Última fecha<br>en que se<br>completó</th>
                @for($i=1; $i<=4; $i++)
                    <th colspan="2">Asignado a</th>
                @endfor
            </tr>
            <tr>
                @for($i=1; $i<=4; $i++)
                    <th>Fecha en que<br>se asignó</th>
                    <th>Fecha en que<br>se completó</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($territories as $territory)
                @php 
                    $assignments = $territory->assignments->values(); 
                    $chunks = $assignments->chunk(4); 
                @endphp

                @if($chunks->isEmpty())
                    <tr style="page-break-inside: avoid;">
                        <td class="text-center" style="font-size: 13px; height: 42px;">{{ $territory->code }}</td>
                        <td class="text-center" style="font-size: 13px;">{{ $territory->last_completed_at?->format('d/m/Y') }}</td>
                        @for($i=0; $i<4; $i++)
                            <td colspan="2" style="padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                    <tr>
                                        <td colspan="2" style="border: none; border-bottom: 1px solid black; height: 21px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%; border: none; border-right: 1px solid black; height: 21px;"></td>
                                        <td style="width: 50%; border: none; height: 21px;"></td>
                                    </tr>
                                </table>
                            </td>
                        @endfor
                    </tr>
                @else
                    @foreach($chunks as $chunk)
                        <tr style="page-break-inside: avoid;">
                            @php $chunk = $chunk->values(); @endphp
                            <td class="text-center" style="font-size: 13px; height: 42px;">{{ $territory->code }}</td>
                            <td class="text-center" style="font-size: 13px;">{{ $territory->last_completed_at?->format('d/m/Y') }}</td>
                            @for($i=0; $i<4; $i++)
                                @if(isset($chunk[$i]))
                                    <td colspan="2" style="padding: 0; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                            <tr>
                                                <td colspan="2" class="text-center" style="border: none; border-bottom: 1px solid black; height: 21px; font-size: 14px; padding: 1px;">{{ $chunk[$i]->assignedTo->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" style="width: 50%; border: none; border-right: 1px solid black; height: 21px; font-size: 13px; padding: 1px;">{{ $chunk[$i]->assigned_at->format('d/m/y') }}</td>
                                                <td class="text-center" style="width: 50%; border: none; height: 21px; font-size: 13px; padding: 1px;">{{ $chunk[$i]->completed_at?->format('d/m/y') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                @else
                                    <td colspan="2" style="padding: 0; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                            <tr>
                                                <td colspan="2" style="border: none; border-bottom: 1px solid black; height: 21px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%; border: none; border-right: 1px solid black; height: 21px;"></td>
                                                <td style="width: 50%; border: none; height: 21px;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                @endif
                            @endfor
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
