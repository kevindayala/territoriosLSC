<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TerritoryAssignment;
use Carbon\Carbon;

class AutocompleteAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:autocomplete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark assignments as completed if they are older than 8 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffTime = Carbon::now()->subHours(8);

        $assignments = TerritoryAssignment::whereNull('completed_at')
            ->where('assigned_at', '<=', $cutoffTime)
            ->get();

        $count = 0;

        foreach ($assignments as $assignment) {
            $assignment->update([
                'completed_at' => Carbon::now(),
            ]);

            // Update territory last_completed_at
            $assignment->territory->update([
                'last_completed_at' => Carbon::now(),
            ]);

            $count++;
        }

        $this->info("Successfully completed {$count} assignments.");
    }
}
