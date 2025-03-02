<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\ExtractedItems\ExtractedItems20250101TableSeeder;
use Database\Seeders\ExtractedItems\ExtractedItems20250201TableSeeder;
use Database\Seeders\ExtractedItems\ExtractedItems20250301TableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Enrich category groups, categories and tags with additional data not present in the original data like description.
        $this->call(CategoryGroupWithCategorySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(TeamSeeder::class);

        // Add item extractions as seeds with orangehill/iseed, which will be added over time for an historical overview.
        // When adding a new extracted items file, do not forget to update the namespace, rename the class, and remove the delete statement.
        $this->call(ExtractedItems20250101TableSeeder::class);
        $this->call(ExtractedItems20250201TableSeeder::class);
        $this->call(ExtractedItems20250301TableSeeder::class);
    }
}
