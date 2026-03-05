<?php

namespace App\Imports;

use App\Models\TerritoryAssignment;
use App\Models\Territory;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Importación: Registros de Asignaciones de Territorios desde archivo Excel.
 *
 * Esta clase procesa cada fila del Excel y crea/actualiza asignaciones de territorios
 * (tabla territory_assignments). Al finalizar, actualiza las fechas de los territorios.
 *
 * Flujo de procesamiento por cada fila:
 * 1. Buscar el territorio por código. Si no existe → omitir fila (sin error).
 * 2. Buscar el usuario asignado por nombre. Si no existe → omitir fila (sin error).
 * 3. Buscar el usuario que asignó por nombre (opcional, usa el usuario actual por defecto).
 * 4. Verificar si ya existe una asignación con el mismo territorio + usuario + fecha.
 *    - Si existe: actualizar los campos (completed_at, tipo, due_date).
 *    - Si no existe: crear una nueva asignación.
 *
 * Comportamiento ante datos no encontrados:
 * - Territorio no encontrado → la fila se omite silenciosamente.
 * - Usuario no encontrado → la fila se omite silenciosamente.
 * - Fecha inválida → la fila se omite silenciosamente.
 * - Todas las omisiones se contabilizan y se reportan en el mensaje de resumen.
 *
 * Post-importación (llamar a updateTerritoryDates()):
 * - Recalcula `last_completed_at` para cada territorio que tuvo filas procesadas.
 * - Esto asegura que el campo "última vez realizado" esté actualizado.
 * - Los territorios con asignaciones activas (sin completed_at) aparecerán como "ocupados".
 *
 * @see \App\Exports\RegistrosBackupExport  Clase que genera el backup compatible
 */
class RegistrosImport implements ToModel, WithHeadingRow
{
    /** @var int Cantidad de registros nuevos creados */
    private $importedCount = 0;

    /** @var int Cantidad de registros existentes actualizados */
    private $updatedCount = 0;

    /** @var int Cantidad de filas omitidas */
    private $skippedCount = 0;

    /** @var array Razones de omisión con sus cantidades. Ej: ["Usuario 'X' no encontrado" => 2] */
    private $skippedReasons = [];

    /** @var array IDs de territorios procesados para actualizar fechas al final */
    private $processedTerritoryIds = [];

    /**
     * Parsear una fecha que puede venir en múltiples formatos.
     *
     * Formatos soportados:
     * - Número de serie de Excel (ej: 44927 → 2023-01-01)
     * - Formato "d/m/Y" (ej: 01/03/2026)
     * - Formato "Y-m-d" (ej: 2026-03-01)
     * - Cualquier formato reconocible por Carbon::parse()
     *
     * @param mixed $value El valor de la celda de fecha.
     * @return \Carbon\Carbon|null La fecha parseada, o null si no es válida.
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Caso 1: Número de serie de Excel (ej: 44927)
        // Excel almacena las fechas como números internamente
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }

        // Caso 2: Formato "d/m/Y" (formato más común en español)
        try {
            return Carbon::createFromFormat('d/m/Y', trim($value));
        } catch (\Exception $e) {
            // Caso 3: Formato "Y-m-d" (formato estándar ISO)
            try {
                return Carbon::createFromFormat('Y-m-d', trim($value));
            } catch (\Exception $e2) {
                // Caso 4: Intentar parseo automático de Carbon
                try {
                    return Carbon::parse(trim($value));
                } catch (\Exception $e3) {
                    return null;
                }
            }
        }
    }

    /**
     * Registrar una razón de omisión para el resumen final.
     *
     * @param string $reason Descripción de por qué se omitió la fila.
     */
    private function addSkipReason(string $reason)
    {
        if (!isset($this->skippedReasons[$reason])) {
            $this->skippedReasons[$reason] = 0;
        }
        $this->skippedReasons[$reason]++;
        $this->skippedCount++;
    }

    /**
     * Procesar una fila del archivo Excel.
     *
     * Campos esperados del Excel (las cabeceras se convierten a snake_case automáticamente):
     * - codigo_territorio / codigo: Código del territorio (obligatorio)
     * - asignado_a: Nombre del usuario al que se asignó el territorio (obligatorio)
     * - asignado_por: Nombre del usuario que hizo la asignación (opcional)
     * - tipo: "Regular" o "Personal" (por defecto: Regular)
     * - fecha_asignacion: Fecha en que se asignó el territorio (obligatorio)
     * - fecha_completado: Fecha en que se completó el territorio (opcional)
     * - fecha_devolucion: Fecha límite de devolución (opcional)
     *
     * Nota: Los campos "ciudad" y "barrio" se exportan como referencia visual
     * pero NO se usan durante la importación (el territorio se busca por código).
     *
     * @param array $row Fila del Excel como array asociativo.
     * @return null Siempre retorna null porque los registros se guardan manualmente.
     */
    public function model(array $row)
    {
        // === PASO 1: Buscar el territorio por código ===
        $territoryCode = trim($row['codigo_territorio'] ?? $row['codigo'] ?? '');

        if (empty($territoryCode)) {
            $this->addSkipReason('Sin código de territorio');
            return null;
        }

        $territory = Territory::where('code', $territoryCode)->first();

        if (!$territory) {
            $this->addSkipReason("Territorio '$territoryCode' no encontrado");
            return null;
        }

        // === PASO 2: Buscar el usuario asignado por nombre ===
        $assignedToName = trim($row['asignado_a'] ?? '');
        if (empty($assignedToName)) {
            $this->addSkipReason('Sin usuario asignado');
            return null;
        }

        $assignedTo = User::where('name', $assignedToName)->first();
        if (!$assignedTo) {
            // Si el usuario no existe, omitir esta fila sin generar error
            $this->addSkipReason("Usuario '$assignedToName' no encontrado");
            return null;
        }

        // === PASO 3: Buscar el usuario que asignó (opcional) ===
        // Si no se encuentra, se usa el usuario autenticado actual o el mismo usuario asignado
        $assignedByName = trim($row['asignado_por'] ?? '');
        $assignedBy = null;
        if (!empty($assignedByName)) {
            $assignedBy = User::where('name', $assignedByName)->first();
        }
        $assignedById = $assignedBy ? $assignedBy->id : (auth()->check() ? auth()->id() : $assignedTo->id);

        // === PASO 4: Determinar el tipo de asignación ===
        $typeRaw = strtolower(trim($row['tipo'] ?? 'regular'));
        $type = in_array($typeRaw, ['personal']) ? 'personal' : 'regular';

        // === PASO 5: Parsear las fechas ===
        $assignedAt = $this->parseDate($row['fecha_asignacion'] ?? null);
        $completedAt = $this->parseDate($row['fecha_completado'] ?? null);
        $dueDate = $this->parseDate($row['fecha_devolucion'] ?? null);

        if (!$assignedAt) {
            $this->addSkipReason('Sin fecha de asignación válida');
            return null;
        }

        // Registrar el territorio para actualizar su fecha al final del import
        $this->processedTerritoryIds[$territory->id] = true;

        // === PASO 6: Crear o actualizar la asignación ===
        // Se identifica una asignación existente por: territorio + usuario + fecha de asignación
        $existingAssignment = TerritoryAssignment::where('territory_id', $territory->id)
            ->where('assigned_to_user_id', $assignedTo->id)
            ->where('assigned_at', $assignedAt->format('Y-m-d'))
            ->first();

        if ($existingAssignment) {
            // Actualizar la asignación existente (ej: agregar fecha de completado)
            $existingAssignment->update([
                'completed_at' => $completedAt,
                'type' => $type,
                'due_date' => $dueDate,
                'assigned_by_user_id' => $assignedById,
            ]);

            $this->updatedCount++;
        } else {
            // Crear nueva asignación
            TerritoryAssignment::create([
                'territory_id' => $territory->id,
                'assigned_to_user_id' => $assignedTo->id,
                'assigned_by_user_id' => $assignedById,
                'assigned_at' => $assignedAt,
                'completed_at' => $completedAt,
                'type' => $type,
                'due_date' => $dueDate,
            ]);

            $this->importedCount++;
        }

        return null; // Los registros ya se guardaron manualmente arriba
    }

    /**
     * Actualizar el campo `last_completed_at` de todos los territorios que fueron procesados.
     *
     * Este método debe llamarse DESPUÉS de que la importación termine (en el controlador).
     * Busca la fecha de completado más reciente de todas las asignaciones de cada territorio
     * y actualiza el campo `last_completed_at` del territorio.
     *
     * Esto asegura que:
     * - El campo "última vez realizado" refleje la realidad después del import.
     * - Los territorios se ordenen correctamente por actividad reciente.
     * - Las estadísticas de la aplicación estén actualizadas.
     */
    public function updateTerritoryDates()
    {
        foreach (array_keys($this->processedTerritoryIds) as $territoryId) {
            $territory = Territory::find($territoryId);
            if (!$territory)
                continue;

            // Obtener la fecha de completado más reciente para este territorio
            $latestCompleted = TerritoryAssignment::where('territory_id', $territoryId)
                ->whereNotNull('completed_at')
                ->orderByDesc('completed_at')
                ->value('completed_at');

            $territory->update([
                'last_completed_at' => $latestCompleted, // Puede ser null si no hay asignaciones completadas
            ]);
        }
    }

    // ========================
    // Métodos de estadísticas
    // ========================

    /** @return int Cantidad de registros nuevos creados durante la importación. */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /** @return int Cantidad de registros existentes que fueron actualizados. */
    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    /** @return int Cantidad total de filas omitidas. */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    /** @return array Razones de omisión con sus cantidades. */
    public function getSkippedReasons(): array
    {
        return $this->skippedReasons;
    }

    /**
     * Generar un mensaje resumen legible para mostrar al usuario después de la importación.
     *
     * Ejemplo de mensaje generado:
     * "Registros importados: 45 nuevo(s), 12 actualizado(s).
     *  Se omitieron 3 fila(s): Usuario 'Juan' no encontrado (2), Territorio 'X99' no encontrado (1).
     *  Se actualizó la fecha de 30 territorio(s)."
     *
     * @return string Mensaje de resumen para mostrar en la interfaz.
     */
    public function getSummaryMessage(): string
    {
        $parts = [];

        if ($this->importedCount > 0) {
            $parts[] = "{$this->importedCount} nuevo(s)";
        }
        if ($this->updatedCount > 0) {
            $parts[] = "{$this->updatedCount} actualizado(s)";
        }

        $msg = 'Registros importados: ' . (empty($parts) ? '0' : implode(', ', $parts)) . '.';

        if ($this->skippedCount > 0) {
            $reasons = [];
            foreach ($this->skippedReasons as $reason => $count) {
                $reasons[] = "$reason ($count)";
            }
            $msg .= " Se omitieron {$this->skippedCount} fila(s): " . implode(', ', $reasons) . '.';
        }

        $territoryCount = count($this->processedTerritoryIds);
        if ($territoryCount > 0) {
            $msg .= " Se actualizó la fecha de {$territoryCount} territorio(s).";
        }

        return $msg;
    }
}
