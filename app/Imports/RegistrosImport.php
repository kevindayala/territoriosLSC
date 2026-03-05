<?php

namespace App\Imports;

use App\Models\TerritoryAssignment;
use App\Models\Territory;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegistrosImport implements ToModel, WithHeadingRow
{
    private $importedCount = 0;
    private $updatedCount = 0;
    private $skippedCount = 0;
    private $skippedReasons = [];
    private $processedTerritoryIds = [];

    /**
     * Parse a date string that can be in d/m/Y or Y-m-d format.
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If it's a numeric value (Excel serial date)
        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }

        // Try d/m/Y format first
        try {
            return Carbon::createFromFormat('d/m/Y', trim($value));
        } catch (\Exception $e) {
            // Try Y-m-d format
            try {
                return Carbon::createFromFormat('Y-m-d', trim($value));
            } catch (\Exception $e2) {
                // Try parsing naturally
                try {
                    return Carbon::parse(trim($value));
                } catch (\Exception $e3) {
                    return null;
                }
            }
        }
    }

    private function addSkipReason(string $reason)
    {
        if (!isset($this->skippedReasons[$reason])) {
            $this->skippedReasons[$reason] = 0;
        }
        $this->skippedReasons[$reason]++;
        $this->skippedCount++;
    }

    public function model(array $row)
    {
        // Find territory by code
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

        // Find user by name
        $assignedToName = trim($row['asignado_a'] ?? '');
        if (empty($assignedToName)) {
            $this->addSkipReason('Sin usuario asignado');
            return null;
        }

        $assignedTo = User::where('name', $assignedToName)->first();
        if (!$assignedTo) {
            $this->addSkipReason("Usuario '$assignedToName' no encontrado");
            return null;
        }

        // Find assigner by name (optional, defaults to current auth user)
        $assignedByName = trim($row['asignado_por'] ?? '');
        $assignedBy = null;
        if (!empty($assignedByName)) {
            $assignedBy = User::where('name', $assignedByName)->first();
        }
        $assignedById = $assignedBy ? $assignedBy->id : (auth()->check() ? auth()->id() : $assignedTo->id);

        // Type
        $typeRaw = strtolower(trim($row['tipo'] ?? 'regular'));
        $type = in_array($typeRaw, ['personal']) ? 'personal' : 'regular';

        // Dates
        $assignedAt = $this->parseDate($row['fecha_asignacion'] ?? null);
        $completedAt = $this->parseDate($row['fecha_completado'] ?? null);
        $dueDate = $this->parseDate($row['fecha_devolucion'] ?? null);

        if (!$assignedAt) {
            $this->addSkipReason('Sin fecha de asignación válida');
            return null;
        }

        // Track territory IDs to update last_completed_at at the end
        $this->processedTerritoryIds[$territory->id] = true;

        // Check if this exact assignment already exists (same territory, user, and assigned_at date)
        $existingAssignment = TerritoryAssignment::where('territory_id', $territory->id)
            ->where('assigned_to_user_id', $assignedTo->id)
            ->where('assigned_at', $assignedAt->format('Y-m-d'))
            ->first();

        if ($existingAssignment) {
            // Update existing
            $existingAssignment->update([
                'completed_at' => $completedAt,
                'type' => $type,
                'due_date' => $dueDate,
                'assigned_by_user_id' => $assignedById,
            ]);

            $this->updatedCount++;
        } else {
            // Create new assignment
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

        return null; // Already saved manually
    }

    /**
     * After import is complete, update last_completed_at for all affected territories.
     */
    public function updateTerritoryDates()
    {
        foreach (array_keys($this->processedTerritoryIds) as $territoryId) {
            $territory = Territory::find($territoryId);
            if (!$territory)
                continue;

            // Get the most recent completed_at date for this territory
            $latestCompleted = TerritoryAssignment::where('territory_id', $territoryId)
                ->whereNotNull('completed_at')
                ->orderByDesc('completed_at')
                ->value('completed_at');

            $territory->update([
                'last_completed_at' => $latestCompleted,
            ]);
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getSkippedReasons(): array
    {
        return $this->skippedReasons;
    }

    /**
     * Build a human-readable summary message.
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
