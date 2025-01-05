<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('at_scouts', function (Blueprint $table) {
            $table->id();
            $table->date('published_at')->unique();
            $table->string('name');
            $table->foreignId('bevers_item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->foreignId('welpen_item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->foreignId('scouts_item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->foreignId('explorers_item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->foreignId('roverscouts_item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->foreignId('extra_item_id')->nullable()->constrained('items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('at_scouts');
    }
};
