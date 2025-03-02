<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The M2M polymorphic relationship for a Team.
        Schema::create('teamables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->morphs('teamable');
            $table->timestamps();

            $table->unique(['team_id', 'teamable_id', 'teamable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teamables');
    }
};
