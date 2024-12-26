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
        Schema::create('extracted_items', function (Blueprint $table) {
            $table->id();
            $table->integer('original_id');
            $table->string('original_slug');
            $table->integer('hits')->default(0);
            $table->mediumText('raw_content');

            $table->timestamp('extracted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('modified_at')->nullable();

            $table->unsignedBigInteger('applied_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracted_items');
    }
};
