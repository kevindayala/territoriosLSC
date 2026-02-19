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
        Schema::create('territories', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // P1, F1, etc
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('neighborhood_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('last_completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['neighborhood_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('territories');
    }
};
