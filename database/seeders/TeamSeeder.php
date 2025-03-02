<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('teams')->delete();

        // Note that this model is newly introduced in this project and thus does not have an original equivalent.

        // Create the teams.
        \DB::table('teams')->insert([
            ['id' => 1, 'name' => 'Spelspecialisten Algemeen'],
            ['id' => 2, 'name' => 'Bevers'],
            ['id' => 3, 'name' => 'Welpen'],
            ['id' => 4, 'name' => 'Scouts'],
            ['id' => 5, 'name' => 'Roplo\'s'],
            ['id' => 6, 'name' => 'Internationaal'],
            ['id' => 7, 'name' => 'Waterscouting'],
            ['id' => 8, 'name' => 'Luchtscouting'],
            ['id' => 9, 'name' => 'Scouts met een Beperking'],
            ['id' => 10, 'name' => 'Rainbowscouting'],
            ['id' => 11, 'name' => 'Scouting Academy'],
            ['id' => 12, 'name' => 'Regiospelen'],
            ['id' => 13, 'name' => 'Communicatie'],
            ['id' => 14, 'name' => 'Financiën'],
            ['id' => 15, 'name' => 'Vrijheid in Herdenken'],
            ['id' => 16, 'name' => 'Buitenleven / MPSE'],
            ['id' => 17, 'name' => 'Internet / JOTA-JOTI'],
            ['id' => 18, 'name' => 'Duurzaamheid'],
            ['id' => 19, 'name' => 'Scouting EHBO / MES'],
            ['id' => 20, 'name' => 'Instructeurs Pionieren'],
            ['id' => 21, 'name' => 'Scouting Arcus Belli'],
            ['id' => 22, 'name' => 'NVVSO'],
        ]);

        // Link categories and tags to teams.
        \DB::table('teamables')->insert([
            // Spelspecialisten Algemeen
            ['team_id' => 1, 'teamable_id' => 88, 'teamable_type' => Tag::class], // Denkdag
            ['team_id' => 1, 'teamable_id' => 21, 'teamable_type' => Tag::class], // Kampactiviteiten
            ['team_id' => 1, 'teamable_id' => 67, 'teamable_type' => Tag::class], // Kennismakingsspel
            ['team_id' => 1, 'teamable_id' => 103, 'teamable_type' => Tag::class], // Last Minute Box
            ['team_id' => 1, 'teamable_id' => 52, 'teamable_type' => Tag::class], // Ouder Kind Opkomst
            ['team_id' => 1, 'teamable_id' => 29, 'teamable_type' => Tag::class], // Scout Scarf Day
            ['team_id' => 1, 'teamable_id' => 96, 'teamable_type' => Tag::class], // Installatie
            ['team_id' => 1, 'teamable_id' => 39, 'teamable_type' => Tag::class], // Tochttechnieken

            // Bevers
            ['team_id' => 2, 'teamable_id' => 1, 'teamable_type' => Category::class], // 5-7 Bevers
            ['team_id' => 2, 'teamable_id' => 159, 'teamable_type' => Tag::class], // Welkom in Hotsjietonia

            // Welpen
            ['team_id' => 3, 'teamable_id' => 2, 'teamable_type' => Category::class], // 7-11 Welpen
            ['team_id' => 3, 'teamable_id' => 130, 'teamable_type' => Tag::class], // Welkom in de Jungle
            ['team_id' => 3, 'teamable_id' => 98, 'teamable_type' => Tag::class], // Jungle Book of Records

            // Scouts
            ['team_id' => 4, 'teamable_id' => 3, 'teamable_type' => Category::class], // 11-15 Scouts
            ['team_id' => 4, 'teamable_id' => 91, 'teamable_type' => Tag::class], // Werken met Ploegen

            // Roplo's
            ['team_id' => 5, 'teamable_id' => 4, 'teamable_type' => Category::class], // 15-18 Explorers
            ['team_id' => 5, 'teamable_id' => 5, 'teamable_type' => Category::class], // 18-21 Roverscouts
            ['team_id' => 5, 'teamable_id' => 136, 'teamable_type' => Tag::class], // Partenza
            ['team_id' => 5, 'teamable_id' => 149, 'teamable_type' => Tag::class], // Powwow

            // Internationaal
            ['team_id' => 6, 'teamable_id' => 8, 'teamable_type' => Category::class], // Internationaal
            ['team_id' => 6, 'teamable_id' => 16, 'teamable_type' => Tag::class], // China
            ['team_id' => 6, 'teamable_id' => 71, 'teamable_type' => Tag::class], // Holi-Phagwa
            ['team_id' => 6, 'teamable_id' => 78, 'teamable_type' => Tag::class], // Keti Koti
            ['team_id' => 6, 'teamable_id' => 77, 'teamable_type' => Tag::class], // Kinderrechten

            // Waterscouting
            ['team_id' => 7, 'teamable_id' => 19, 'teamable_type' => Category::class], // Op en om het water
            ['team_id' => 7, 'teamable_id' => 11, 'teamable_type' => Tag::class], // Binnenvaart
            ['team_id' => 7, 'teamable_id' => 6, 'teamable_type' => Tag::class], // CWO
            ['team_id' => 7, 'teamable_id' => 10, 'teamable_type' => Tag::class], // CWO Buitenboord Motor
            ['team_id' => 7, 'teamable_id' => 7, 'teamable_type' => Tag::class], // CWO Kielboot
            ['team_id' => 7, 'teamable_id' => 8, 'teamable_type' => Tag::class], // CWO Roeiboot
            ['team_id' => 7, 'teamable_id' => 9, 'teamable_type' => Tag::class], // CWO Sloep/Motorvlet
            ['team_id' => 7, 'teamable_id' => 46, 'teamable_type' => Tag::class], // Waterschappen
            ['team_id' => 7, 'teamable_id' => 4, 'teamable_type' => Tag::class], // Waterscouting
            ['team_id' => 7, 'teamable_id' => 5, 'teamable_type' => Tag::class], // Waterspelen
            ['team_id' => 7, 'teamable_id' => 94, 'teamable_type' => Tag::class], // Wedstrijdzeilen
            ['team_id' => 7, 'teamable_id' => 119, 'teamable_type' => Tag::class], // Zwemmen

            // Luchtscouting
            ['team_id' => 8, 'teamable_id' => 30, 'teamable_type' => Tag::class], // Luchtscouting

            // Scouts met een Beperking
            ['team_id' => 9, 'teamable_id' => 108, 'teamable_type' => Tag::class], // Gebaren
            ['team_id' => 9, 'teamable_id' => 125, 'teamable_type' => Tag::class], // Taalspellen
            ['team_id' => 9, 'teamable_id' => 106, 'teamable_type' => Tag::class], // Rust en Ontspanning
            ['team_id' => 9, 'teamable_id' => 104, 'teamable_type' => Tag::class], // Visueel
            ['team_id' => 9, 'teamable_id' => 135, 'teamable_type' => Tag::class], // Geuren

            // Rainbowscouting
            ['team_id' => 10, 'teamable_id' => 49, 'teamable_type' => Tag::class], // Gender
            ['team_id' => 10, 'teamable_id' => 50, 'teamable_type' => Tag::class], // Homoseksualiteit
            ['team_id' => 10, 'teamable_id' => 102, 'teamable_type' => Tag::class], // Pride
            ['team_id' => 10, 'teamable_id' => 48, 'teamable_type' => Tag::class], // Rainbowscouting
            ['team_id' => 10, 'teamable_id' => 41, 'teamable_type' => Tag::class], // SDG 5 Gendergelijkheid

            // Scouting Academy
            ['team_id' => 11, 'teamable_id' => 73, 'teamable_type' => Tag::class], // Brainstormen
            ['team_id' => 11, 'teamable_id' => 64, 'teamable_type' => Tag::class], // Complimentendag
            ['team_id' => 11, 'teamable_id' => 51, 'teamable_type' => Tag::class], // Sociale Veiligheid
            ['team_id' => 11, 'teamable_id' => 43, 'teamable_type' => Tag::class], // Vrijwilligers

            // Regiospelen
            ['team_id' => 12, 'teamable_id' => 101, 'teamable_type' => Tag::class], // Regiospelen

            // Communicatie
            ['team_id' => 13, 'teamable_id' => 38, 'teamable_type' => Tag::class], // Tijd voor Avontuur
            ['team_id' => 13, 'teamable_id' => 44, 'teamable_type' => Tag::class], // Open Dag

            // Financiën
            ['team_id' => 14, 'teamable_id' => 63, 'teamable_type' => Tag::class], // Financiële Acties
            ['team_id' => 14, 'teamable_id' => 69, 'teamable_type' => Tag::class], // Serious Request
            ['team_id' => 14, 'teamable_id' => 105, 'teamable_type' => Tag::class], // Monopoly

            // Vrijheid in Herdenken
            ['team_id' => 15, 'teamable_id' => 23, 'teamable_type' => Tag::class], // 4 en 5 Mei
            ['team_id' => 15, 'teamable_id' => 47, 'teamable_type' => Tag::class], // Vluchtelingen
            ['team_id' => 15, 'teamable_id' => 57, 'teamable_type' => Tag::class], // Vredeslicht
            ['team_id' => 15, 'teamable_id' => 24, 'teamable_type' => Tag::class], // SDG 16 Vrede, justitie en sterke publieke diensten

            // Buitenleven / MPSE
            ['team_id' => 16, 'teamable_id' => 18, 'teamable_type' => Tag::class], // Modderdag
            ['team_id' => 16, 'teamable_id' => 40, 'teamable_type' => Tag::class], // Natuurwerkdag
            ['team_id' => 16, 'teamable_id' => 31, 'teamable_type' => Tag::class], // Nacht van de Nacht
            ['team_id' => 16, 'teamable_id' => 113, 'teamable_type' => Tag::class], // Vogels
            ['team_id' => 16, 'teamable_id' => 100, 'teamable_type' => Tag::class], // Vuurtechnieken
            ['team_id' => 16, 'teamable_id' => 164, 'teamable_type' => Tag::class], // Primitief Koken

            // Internet / JOTA-JOTI
            ['team_id' => 17, 'teamable_id' => 75, 'teamable_type' => Tag::class], // Communicatie
            ['team_id' => 17, 'teamable_id' => 114, 'teamable_type' => Tag::class], // Internetveiligheid
            ['team_id' => 17, 'teamable_id' => 72, 'teamable_type' => Tag::class], // IRC Spelen
            ['team_id' => 17, 'teamable_id' => 1, 'teamable_type' => Tag::class], // JOTA-JOTI

            // Duurzaamheid
            ['team_id' => 18, 'teamable_id' => 65, 'teamable_type' => Tag::class], // Duurzame Ontwikkelingsdoelen
            ['team_id' => 18, 'teamable_id' => 37, 'teamable_type' => Tag::class], // SDG 7 Betaalbare en duurzame energie
            ['team_id' => 18, 'teamable_id' => 33, 'teamable_type' => Tag::class], // SDG 12 Verantwoorde consumptie en productie
            ['team_id' => 18, 'teamable_id' => 26, 'teamable_type' => Tag::class], // SDG 13 Klimaatactie
            ['team_id' => 18, 'teamable_id' => 20, 'teamable_type' => Tag::class], // SDG 15 Leven op het land
            ['team_id' => 18, 'teamable_id' => 27, 'teamable_type' => Tag::class], // SDG 14 Leven in het water
            ['team_id' => 18, 'teamable_id' => 59, 'teamable_type' => Tag::class], // Earth Hour
            ['team_id' => 18, 'teamable_id' => 66, 'teamable_type' => Tag::class], // Zaaien en Kweken

            // Scouting EHBO / MES
            ['team_id' => 19, 'teamable_id' => 22, 'teamable_type' => Tag::class], // EHBO
            ['team_id' => 19, 'teamable_id' => 97, 'teamable_type' => Tag::class], // Fysieke Veiligheid

            // Instructeurs Pionieren
            ['team_id' => 20, 'teamable_id' => 32, 'teamable_type' => Tag::class], // Minipionieren
            ['team_id' => 20, 'teamable_id' => 62, 'teamable_type' => Tag::class], // Pionieren

            // Scouting Arcus Belli
            ['team_id' => 21, 'teamable_id' => 137, 'teamable_type' => Tag::class], // Boogschieten

            // NVVSO
            ['team_id' => 22, 'teamable_id' => 120, 'teamable_type' => Tag::class], // Scoutinggeschiedenis
        ]);

        // Note: to add users to the teams we also need the users. However, they are created when processing extracted items and
        // thus are not available during seeding just yet. Therefore, connect users to teams manually after processing for now.
        // TODO Find a less manual setup.
    }
}
