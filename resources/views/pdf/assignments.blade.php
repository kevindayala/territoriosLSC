<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Registro de Asignación de Territorio</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid black; padding: 4px; vertical-align: middle; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .title { text-align: center; margin-bottom: 20px; }
        .page-break { page-break-after: always; }
        
        /* Column widths */
        .col-terr { width: 8%; }
        .col-date { width: 10%; }
        .col-assign-name { width: 12%; }
        .col-assign-dates { width: 8%; }
    </style>
</head>
<body>
    <div class="title">
        <h1>REGISTRO DE ASIGNACIÓN DE TERRITORIO</h1>
        <h3>Año de servicio: {{ $year }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-terr" rowspan="2">Núm. de<br>terr.</th>
                <th class="col-date" rowspan="2">Última fecha<br>completado</th>
                @for($i=1; $i<=4; $i++)
                    <th colspan="2">Asignado a</th>
                @endfor
            </tr>
            <tr>
                @for($i=1; $i<=4; $i++)
                    <th class="col-assign-name">Nombre</th>
                    <th class="col-assign-dates">Asig / Comp</th>
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
                    <tr>
                        <td class="text-center font-bold">{{ $territory->code }}</td>
                        <td class="text-center">{{ $territory->last_completed_at?->format('d/m/Y') }}</td>
                        @for($i=0; $i<4; $i++)
                            <td></td><td></td>
                        @endfor
                    </tr>
                @else
                    @foreach($chunks as $chunk)
                        <tr>
                            <td class="text-center font-bold">{{ $territory->code }}</td>
                            <td class="text-center">{{ $territory->last_completed_at?->format('d/m/Y') }}</td>
                            @for($i=0; $i<4; $i++)
                                @if(isset($chunk[$i]))
                                    <td>{{ $chunk[$i]->assignedTo->name }}</td>
                                    <td class="text-center" style="font-size: 8px;">
                                        {{ $chunk[$i]->assigned_at->format('d/m/y') }}<br>
                                        <hr style="margin: 2px 0; border: 0; border-top: 1px dotted #ccc;">
                                        {{ $chunk[$i]->completed_at?->format('d/m/y') ?? '-' }}
                                    </td>
                                @else
                                    <td></td><td></td>
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
