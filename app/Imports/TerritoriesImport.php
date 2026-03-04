<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Territory;
use App\Models\Person;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TerritoriesImport implements ToModel, WithHeadingRow
{
    private function fixEncoding($text)
    {
        if (empty($text)) {
            return '';
        }

        // Si el texto parece ser un "Mojibake" (Doble codificación UTF-8, común en Excel CSV)
        // Por ejemplo, "Ã±" en lugar de "ñ", "Ã¡" en lugar de "á"
        $mojibakeIndicators = ['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã±', 'Ã', 'Ã‰', 'Ã', 'Ã“', 'Ãš', 'Ã‘', 'Â¿', 'Â¡', 'Â°'];

        foreach ($mojibakeIndicators as $indicator) {
            if (strpos($text, $indicator) !== false) {
                // Decodificar la doble codificación convirtiéndolo a Windows-1252
                $fixed = @mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
                if ($fixed !== false) {
                    $text = $fixed;
                }
                break; // Una vez arreglado, salir del bucle
            }
        }

        // Asegurarse de que el string final es UTF-8 válido
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }

        return $text;
    }

    public function model(array $row)
    {
        // El código de territorio o de las columnas de sordo
        $territoryCode = trim($row['codigo_territorio'] ?? $row['codigo'] ?? '');

        if (empty($territoryCode)) {
            return null;
        }

        // 1. Encontrar o crear la ciudad
        $cityNameRaw = trim($this->fixEncoding($row['nombre_de_ciudad'] ?? $row['ciudad'] ?? ''));
        if (empty($cityNameRaw)) {
            return null; // El territorio necesita ciudad
        }

        // Limpiar posible prefijo visual "— " copiado de la interfaz
        $cityNameRaw = preg_replace('/^[—\-\s]+/', '', $cityNameRaw);

        if (strpos($cityNameRaw, '>') !== false) {
            $parts = explode('>', $cityNameRaw);
            $parentName = mb_substr(trim($parts[0]), 0, 255);
            $childName = mb_substr(trim($parts[1]), 0, 255);

            $parentCity = City::firstOrCreate(
                ['name' => $parentName],
                ['slug' => Str::slug($parentName), 'is_active' => true]
            );

            $city = City::firstOrCreate(
                ['name' => $childName, 'parent_id' => $parentCity->id],
                ['slug' => Str::slug($childName), 'is_active' => true]
            );
        } else {
            $cityNameRaw = mb_substr($cityNameRaw, 0, 255);
            $city = City::where('name', $cityNameRaw)->first();
            if (!$city) {
                $city = City::create([
                    'name' => $cityNameRaw,
                    'slug' => Str::slug($cityNameRaw),
                    'is_active' => true
                ]);
            }
        }

        // 2. Estado de Territorio
        $statusRaw = strtolower(trim($row['estado_territorio'] ?? $row['estado'] ?? 'activo'));
        $status = 'active';
        if (in_array($statusRaw, ['inactivo', 'inactive'])) {
            $status = 'inactive';
        }

        // 3. Crear o actualizar Territorio
        $territory = Territory::updateOrCreate(
            ['code' => $territoryCode],
            [
                'city_id' => $city->id,
                'neighborhood_name' => mb_substr(trim($this->fixEncoding($row['nombre_de_barrio'] ?? '')), 0, 255),
                'status' => $status,
                'notes' => mb_substr(trim($this->fixEncoding($row['notas_territorio'] ?? $row['notas'] ?? '')), 0, 65535),
            ]
        );

        // 4. Manejar Persona Sorda (si la fila tiene datos de Sordo)
        $personName = mb_substr(trim($this->fixEncoding($row['sordo_nombre'] ?? '')), 0, 255);
        $personAddress = mb_substr(trim($this->fixEncoding($row['sordo_direccion'] ?? '')), 0, 255);

        if ($personName) {
            // Buscamos a la persona en base a su nombre y el territorio
            $person = Person::firstOrNew([
                'territory_id' => $territory->id,
                'full_name' => $personName
            ]);

            $person->address = $personAddress;
            $person->map_url = mb_substr(trim($this->fixEncoding($row['sordo_mapa_url'] ?? '')), 0, 500);
            $person->notes = mb_substr(trim($this->fixEncoding($row['sordo_notas'] ?? '')), 0, 65535);

            $personStatusRaw = strtolower(trim($row['sordo_estado'] ?? 'activo'));
            $personStatus = 'active';
            if (in_array($personStatusRaw, ['pendiente', 'pending']))
                $personStatus = 'pending';
            if (in_array($personStatusRaw, ['inactivo', 'inactive']))
                $personStatus = 'inactive';

            $person->status = $personStatus;

            // Si se estÃ¡ aprobando via Excel
            if (!$person->exists || !$person->approved_at) {
                if ($person->status === 'active') {
                    $person->approved_at = now();
                    if (auth()->check()) {
                        $person->approved_by_user_id = auth()->id();
                    }
                }
            }

            if (!$person->exists && auth()->check()) {
                $person->created_by_user_id = auth()->id();
            }

            $person->save();
        }

        return $territory;
    }
}
