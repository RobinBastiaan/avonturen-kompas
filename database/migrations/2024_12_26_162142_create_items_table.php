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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->string('title');
            $table->string('slug');
            $table->string('hash', 6);
            $table->boolean('is_camp')->default(false);
            $table->integer('camp_length')->nullable();
            $table->text('summary');
            $table->mediumText('description');
            $table->text('requirements')->nullable();
            $table->text('tips')->nullable();
            $table->text('safety')->nullable();
            $table->unsignedInteger('hits')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
