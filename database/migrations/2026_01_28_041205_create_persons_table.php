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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->text('address');
            $table->string('map_url')->nullable();
            $table->foreignId('territory_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending'); // 'pending' for created by publicador
            $table->text('inactive_reason_note')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
