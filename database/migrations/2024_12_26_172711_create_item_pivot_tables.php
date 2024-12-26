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
        // Category-Item pivot table.
        Schema::create('category_item', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'item_id']);
        });

        // Item-Tag pivot table.
        Schema::create('item_tag', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['item_id', 'tag_id']);
        });

        // Item-User (favorites) pivot table.
        Schema::create('item_user', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['item_id', 'user_id']);
        });

        // Item-Item (camp activities) pivot table.
        Schema::create('camp_activities', function (Blueprint $table) {
            $table->foreignId('camp_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('items')->cascadeOnDelete();
            $table->integer('day_number');
            $table->integer('sort_order');
            $table->primary(['camp_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_item');
        Schema::dropIfExists('item_tag');
        Schema::dropIfExists('item_user');
        Schema::dropIfExists('camp_activities');
    }
};
