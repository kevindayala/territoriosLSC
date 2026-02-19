<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('territories', function (Blueprint $table) {
            $table->string('neighborhood_name')->nullable()->after('city_id');
        });

        // Migrate existing data
        DB::statement('UPDATE territories t JOIN neighborhoods n ON t.neighborhood_id = n.id SET t.neighborhood_name = n.name');

        Schema::table('territories', function (Blueprint $table) {
            // Make neighborhood_name required after migration
            $table->string('neighborhood_name')->nullable(false)->change();

            // Drop foreign key and column
            $table->dropForeign(['neighborhood_id']);
            $table->dropColumn('neighborhood_id');
        });

        Schema::dropIfExists('neighborhoods');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate neighborhoods table
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('territories', function (Blueprint $table) {
            $table->foreignId('neighborhood_id')->nullable()->after('city_id')->constrained();
        });

        // Attempt to restore data (will create new IDs unfortunately, no way to restore exact IDs easily without more complex logic)
        // This is a comprehensive down that attempts to restore structural integrity at loss of exact relational ID mapping if deleted
        // Ideally we would back up, but for this task scope, structure restore is primary.

        // Re-populate neighborhoods from unique names in territories
        $territories = DB::table('territories')->select('neighborhood_name', 'city_id')->distinct()->get();
        foreach ($territories as $t) {
            $nId = DB::table('neighborhoods')->insertGetId([
                'name' => $t->neighborhood_name,
                'city_id' => $t->city_id,
                'slug' => \Illuminate\Support\Str::slug($t->neighborhood_name . '-' . $t->city_id),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('territories')
                ->where('neighborhood_name', $t->neighborhood_name)
                ->where('city_id', $t->city_id)
                ->update(['neighborhood_id' => $nId]);
        }

        Schema::table('territories', function (Blueprint $table) {
            $table->dropColumn('neighborhood_name');
            $table->foreignId('neighborhood_id')->nullable(false)->change();
        });
    }
};
