<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Territory;
use App\Models\Person;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Importación: Territorios + Personas (Sordos) desde archivo Excel.
 *
 * Esta clase procesa cada fila del Excel y crea/actualiza territorios y personas.
 *
 * Flujo de procesamiento por cada fila:
 * 1. Leer el código de territorio (obligatorio, sino se omite la fila).
 * 2. Buscar o crear la ciudad (soporta jerarquía padre > hijo).
 * 3. Crear o actualizar el territorio (identificado por su código único).
 * 4. Si la fila incluye datos de una persona (sordo), crearla o actualizarla.
 *
 * Manejo de ciudades con jerarquía:
 * - Formato "Padre > Hijo": Se crea la ciudad padre y luego la ciudad hija con parent_id.
 *   Ejemplo: "Santander > Florida" → Crea "Santander" como padre y "Florida" como hija.
 * - Formato solo nombre: Se busca/crea la ciudad directamente.
 * - Si existe el campo "Localidad (Opcional)": Se usa como ciudad hija del campo "Ciudad".
 *
 * Manejo de encoding:
 * Los archivos CSV de Excel a veces tienen problemas de codificación (Mojibake).
 * El método fixEncoding() detecta y corrige estos problemas automáticamente.
 * Ejemplo: "Ã±" se corrige a "ñ", "Ã¡" se corrige a "á".
 *
 * @see \App\Exports\TerritoriesBackupExport   Clase que genera el backup compatible
 * @see \App\Exports\TerritoriesTemplateExport  Plantilla vacía con formato esperado
 */
class TerritoriesImport implements ToModel, WithHeadingRow
{
    /**
     * Corregir problemas de encoding (Mojibake) comunes en archivos CSV de Excel.
     *
     * Excel a veces guarda archivos CSV con doble codificación UTF-8 → Windows-1252.
     * Esto causa que caracteres especiales como "ñ" y acentos se corrompan.
     *
     * @param string|null $text Texto potencialmente corrupto.
     * @return string Texto corregido en UTF-8 válido.
     */
    private function fixEncoding($text)
    {
        if (empty($text)) {
            return '';
        }

        // Detectar indicadores de Mojibake (doble codificación UTF-8)
        // Ejemplo: "Ã±" debería ser "ñ", "Ã¡" debería ser "á"
        // Solo se necesitan algunos patrones comunes: si cualquiera se detecta,
        // se aplica la corrección a toda la cadena completa.
        // Nota: Los indicadores de mayúsculas (Á,É,Í,Ó,Ú,Ñ) se omiten porque
        // sus bytes Win-1252 producen comillas tipográficas que rompen la sintaxis PHP.
        $mojibakeIndicators = ['Ã¡', 'Ã©', 'Ã³', 'Ãº', 'Ã±', 'Â¿', 'Â¡'];

        foreach ($mojibakeIndicators as $indicator) {
            if (strpos($text, $indicator) !== false) {
                // Decodificar la doble codificación: UTF-8 → Windows-1252
                $fixed = @mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
                if ($fixed !== false) {
                    $text = $fixed;
                }
                break;
            }
        }

        // Verificar que el resultado final es UTF-8 válido
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }

        return $text;
    }

    /**
     * Procesar una fila del archivo Excel.
     *
     * Cada fila puede contener datos de un territorio y opcionalmente datos de una persona.
     * Múltiples filas pueden compartir el mismo código de territorio (una persona por fila).
     *
     * Campos esperados del Excel (las cabeceras se convierten a snake_case automáticamente):
     * - codigo_territorio / codigo: Código único del territorio (obligatorio)
     * - nombre_de_ciudad / ciudad: Nombre de la ciudad (obligatorio)
     * - localidad_opcional / localidad: Sub-localidad (opcional, crea jerarquía padre>hijo)
     * - nombre_de_barrio: Nombre del barrio
     * - estado_territorio / estado: "Activo" o "Inactivo"
     * - notas_territorio / notas: Notas del territorio
     * - sordo_nombre: Nombre de la persona sorda (si está vacío, no se crea persona)
     * - sordo_direccion: Dirección de la persona
     * - sordo_mapa_url: URL de Google Maps
     * - sordo_notas: Notas sobre la persona
     * - sordo_estado: "Activo", "Pendiente" o "Inactivo"
     *
     * @param array $row Fila del Excel como array asociativo.
     * @return \App\Models\Territory|null El territorio creado/actualizado, o null si la fila no es válida.
     */
    public function model(array $row)
    {
        // === PASO 1: Obtener código de territorio (obligatorio) ===
        $territoryCode = trim($row['codigo_territorio'] ?? $row['codigo'] ?? '');

        if (empty($territoryCode)) {
            return null;
        }

        // === PASO 2: Buscar o crear la ciudad ===
        $cityNameRaw = trim($this->fixEncoding($row['nombre_de_ciudad'] ?? $row['ciudad'] ?? ''));
        $localityRaw = trim($this->fixEncoding($row['localidad_opcional'] ?? $row['localidad'] ?? ''));

        if (empty($cityNameRaw)) {
            return null; // El territorio necesita una ciudad
        }

        // Limpiar posible prefijo visual "— " copiado de la interfaz
        $cityNameRaw = preg_replace('/^[—\-\s]+/', '', $cityNameRaw);
        $localityRaw = preg_replace('/^[—\-\s]+/', '', $localityRaw);

        // Caso 1: Ciudad con jerarquía "Padre > Hijo" en un solo campo
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
        }
        // Caso 2: Ciudad y localidad en campos separados
        elseif (!empty($localityRaw)) {
            $parentName = mb_substr($cityNameRaw, 0, 255);
            $childName = mb_substr($localityRaw, 0, 255);

            $parentCity = City::firstOrCreate(
                ['name' => $parentName],
                ['slug' => Str::slug($parentName), 'is_active' => true]
            );

            $city = City::firstOrCreate(
                ['name' => $childName, 'parent_id' => $parentCity->id],
                ['slug' => Str::slug($childName), 'is_active' => true]
            );
        }
        // Caso 3: Ciudad simple sin jerarquía
        else {
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

        // === PASO 3: Crear o actualizar el territorio ===
        // Se usa updateOrCreate con el código como clave única
        $statusRaw = strtolower(trim($row['estado_territorio'] ?? $row['estado'] ?? 'activo'));
        $status = in_array($statusRaw, ['inactivo', 'inactive']) ? 'inactive' : 'active';

        $territory = Territory::updateOrCreate(
            ['code' => $territoryCode],
            [
                'city_id' => $city->id,
                'neighborhood_name' => mb_substr(trim($this->fixEncoding($row['nombre_de_barrio'] ?? '')), 0, 255),
                'status' => $status,
                'notes' => mb_substr(trim($this->fixEncoding($row['notas_territorio'] ?? $row['notas'] ?? '')), 0, 65535),
            ]
        );

        // === PASO 4: Crear o actualizar persona sorda (si la fila tiene datos) ===
        $personName = mb_substr(trim($this->fixEncoding($row['sordo_nombre'] ?? '')), 0, 255);
        $personAddress = mb_substr(trim($this->fixEncoding($row['sordo_direccion'] ?? '')), 0, 255);

        if ($personName) {
            // Buscar persona por nombre + territorio (evitar duplicados)
            $person = Person::firstOrNew([
                'territory_id' => $territory->id,
                'full_name' => $personName
            ]);

            $person->address = $personAddress;
            $person->map_url = mb_substr(trim($this->fixEncoding($row['sordo_mapa_url'] ?? '')), 0, 500);
            $person->notes = mb_substr(trim($this->fixEncoding($row['sordo_notas'] ?? '')), 0, 65535);

            // Traducir estado de la persona
            $personStatusRaw = strtolower(trim($row['sordo_estado'] ?? 'activo'));
            $personStatus = 'active';
            if (in_array($personStatusRaw, ['pendiente', 'pending']))
                $personStatus = 'pending';
            if (in_array($personStatusRaw, ['inactivo', 'inactive']))
                $personStatus = 'inactive';

            $person->status = $personStatus;

            // Auto-aprobar personas activas que no tienen fecha de aprobación
            if (!$person->exists || !$person->approved_at) {
                if ($person->status === 'active') {
                    $person->approved_at = now();
                    if (auth()->check()) {
                        $person->approved_by_user_id = auth()->id();
                    }
                }
            }

            // Asignar el usuario creador si es una persona nueva
            if (!$person->exists && auth()->check()) {
                $person->created_by_user_id = auth()->id();
            }

            $person->save();
        }

        return $territory;
    }
}
