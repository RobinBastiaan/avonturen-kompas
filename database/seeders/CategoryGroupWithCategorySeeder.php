<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoryGroupWithCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('categories')->delete();
        \DB::table('category_groups')->delete();

        // Seed category_groups with: name, description, is_available_for_activities, is_available_for_camps
        // Seed categories with: name, description, category_group_id
        // Note that the original categories do not have a description, so we add them here in a seeder.

        \DB::table('category_groups')->insert([
            0 => [
                'id'                          => 1,
                'is_published'                => 1,
                'name'                        => 'Leeftijdsgroep',
                'description'                 => 'De leeftijdscategorie (ookwel speltak genoemd) waarvoor de activiteit geschikt is',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
            1 => [
                'id'                          => 2,
                'is_published'                => 1,
                'name'                        => 'Activiteitengebied',
                'description'                 => 'Indeling van acht gebieden om activiteiten die qua onderwerp bij elkaar horen onder te verdelen voor een gevarieerd programma',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
            2 => [
                'id'                          => 3,
                'is_published'                => 1,
                'name'                        => 'Locatie',
                'description'                 => 'De plek waar de activiteit kan worden uitgevoerd',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
            3 => [
                'id'                          => 4,
                'is_published'                => 1,
                'name'                        => 'Duur',
                'description'                 => 'De geschatte tijdsduur van een activiteit',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 0,
                'is_required'                 => 1,
            ],
            4 => [
                'id'                          => 5,
                'is_published'                => 1,
                'name'                        => 'Groepsgrootte',
                'description'                 => 'Het aantal deelnemers waarvoor de activiteit geschikt is',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
            5 => [
                'id'                          => 6,
                'is_published'                => 1,
                'name'                        => 'Voorbereidingstijd',
                'description'                 => 'De tijd die nodig is om de activiteit voor te bereiden',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
            6 => [
                'id'                          => 7,
                'is_published'                => 1,
                'name'                        => 'Toekomstthema\'s',
                'description'                 => 'De vijf toekomstthema\'s in de toekomstvisie t/m 2025',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 0,
            ],
            7 => [
                'id'                          => 8,
                'is_published'                => 1,
                'name'                        => 'Kwalificatie',
                'description'                 => 'Activiteiten ter voorbereiding voor officiële kwalificaties',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 0,
            ],
            8 => [
                'id'                          => 9,
                'is_published'                => 1,
                'name'                        => 'Awards',
                'description'                 => 'Pluspakket naast de normale insignes en badges voor scouts, explorers en roverscouts die meer willen doen, zien en ervaren',
                'is_available_for_activities' => 1,
                'is_available_for_camps'      => 1,
                'is_required'                 => 0,
            ],
            9 => [
                'id'                          => 10,
                'is_published'                => 1,
                'name'                        => 'Duur van het kamp',
                'description'                 => 'De lengte van het kamp in dagen of nachten',
                'is_available_for_activities' => 0,
                'is_available_for_camps'      => 1,
                'is_required'                 => 1,
            ],
        ]);

        \DB::table('categories')->insert([
            0  => [
                'id'                => 1,
                'is_published'      => 1,
                'name'              => '5-7 Bevers',
                'description'       => 'Activiteiten of kampen specifiek voor bevers in de leeftijd van 5 tot 7 jaar',
                'category_group_id' => 1,
            ],
            1  => [
                'id'                => 2,
                'is_published'      => 1,
                'name'              => '7-11 Welpen',
                'description'       => 'Activiteiten of kampen specifiek voor welpen in de leeftijd van 7 tot 11 jaar',
                'category_group_id' => 1,
            ],
            2  => [
                'id'                => 3,
                'is_published'      => 1,
                'name'              => '11-15 Scouts',
                'description'       => 'Activiteiten of kampen specifiek voor scouts in de leeftijd van 11 tot 15 jaar',
                'category_group_id' => 1,
            ],
            3  => [
                'id'                => 4,
                'is_published'      => 1,
                'name'              => '15-18 Explorers',
                'description'       => 'Activiteiten of kampen specifiek voor explorers in de leeftijd van 15 tot 18 jaar',
                'category_group_id' => 1,
            ],
            4  => [
                'id'                => 5,
                'is_published'      => 1,
                'name'              => '18-21 Roverscouts',
                'description'       => 'Activiteiten of kampen specifiek voor roverscouts in de leeftijd van 18 tot 21 jaar',
                'category_group_id' => 1,
            ],
            5  => [
                'id'                => 6,
                'is_published'      => 1,
                'name'              => 'Kader',
                'description'       => 'Activiteiten of kampen specifiek voor leidinggevenden en bestuurders',
                'category_group_id' => 1,
            ],
            6  => [
                'id'                => 7,
                'is_published'      => 1,
                'name'              => 'Alle leeftijden samen',
                'description'       => 'Activiteiten of kampen geschikt voor alle leeftijdsgroepen tegelijk',
                'category_group_id' => 1,
            ],
            7  => [
                'id'                => 8,
                'is_published'      => 1,
                'name'              => 'Internationaal',
                'description'       => 'Activiteiten of kampen met een internationale dimensie of samenwerking',
                'category_group_id' => 2,
            ],
            8  => [
                'id'                => 9,
                'is_published'      => 1,
                'name'              => 'Sport & Spel',
                'description'       => 'Actieve activiteiten of kampen gericht op beweging, sport en spelvormen',
                'category_group_id' => 2,
            ],
            9  => [
                'id'                => 10,
                'is_published'      => 1,
                'name'              => 'Samenleving',
                'description'       => 'Activiteiten of kampen die betrekking hebben op maatschappelijke thema\'s en burgerschap',
                'category_group_id' => 2,
            ],
            10 => [
                'id'                => 11,
                'is_published'      => 1,
                'name'              => 'Uitdagende Scoutingtechnieken',
                'description'       => 'Activiteiten of kampen gericht op het leren en toepassen van specifieke scoutingvaardigheden',
                'category_group_id' => 2,
            ],
            11 => [
                'id'                => 12,
                'is_published'      => 1,
                'name'              => 'Expressie',
                'description'       => 'Creatieve en kunstzinnige activiteiten of kampen',
                'category_group_id' => 2,
            ],
            12 => [
                'id'                => 13,
                'is_published'      => 1,
                'name'              => 'Veilig & Gezond',
                'description'       => 'Activiteiten of kampen gericht op veiligheid, gezondheid en welzijn',
                'category_group_id' => 2,
            ],
            13 => [
                'id'                => 14,
                'is_published'      => 1,
                'name'              => 'Buitenleven',
                'description'       => 'Activiteiten of kampen gericht op natuur, survival en buitenleven',
                'category_group_id' => 2,
            ],
            14 => [
                'id'                => 15,
                'is_published'      => 1,
                'name'              => 'Identiteit',
                'description'       => 'Activiteiten of kampen gericht op persoonlijke ontwikkeling en scoutingidentiteit',
                'category_group_id' => 2,
            ],
            15 => [
                'id'                => 16,
                'is_published'      => 1,
                'name'              => 'Buiten',
                'description'       => 'Activiteiten die in de buitenlucht worden uitgevoerd',
                'category_group_id' => 3,
            ],
            16 => [
                'id'                => 17,
                'is_published'      => 1,
                'name'              => '1,5 meter',
                'description'       => 'Activiteiten die geschikt zijn voor het houden van 1,5 meter afstand',
                'category_group_id' => 3,
            ],
            17 => [
                'id'                => 18,
                'is_published'      => 1,
                'name'              => 'Binnen',
                'description'       => 'Activiteiten die binnen worden uitgevoerd',
                'category_group_id' => 3,
            ],
            18 => [
                'id'                => 19,
                'is_published'      => 1,
                'name'              => 'Op en om het water',
                'description'       => 'Activiteiten die op of rond het water plaatsvinden',
                'category_group_id' => 3,
            ],
            19 => [
                'id'                => 20,
                'is_published'      => 1,
                'name'              => 'Rondom het clubhuis',
                'description'       => 'Activiteiten die in en rond het scoutinggebouw plaatsvinden',
                'category_group_id' => 3,
            ],
            20 => [
                'id'                => 21,
                'is_published'      => 1,
                'name'              => 'Grasveld, speelveld',
                'description'       => 'Activiteiten die op een open veld of speelterrein worden uitgevoerd',
                'category_group_id' => 3,
            ],
            21 => [
                'id'                => 22,
                'is_published'      => 1,
                'name'              => 'Bos',
                'description'       => 'Activiteiten die specifiek in een bosrijke omgeving plaatsvinden',
                'category_group_id' => 3,
            ],
            22 => [
                'id'                => 23,
                'is_published'      => 1,
                'name'              => 'Stad',
                'description'       => 'Activiteiten die in een stedelijke omgeving plaatsvinden',
                'category_group_id' => 3,
            ],
            23 => [
                'id'                => 24,
                'is_published'      => 1,
                'name'              => 'Online',
                'description'       => 'Activiteiten die digitaal of via internet worden uitgevoerd',
                'category_group_id' => 3,
            ],
            24 => [
                'id'                => 25,
                'is_published'      => 1,
                'name'              => 'Overig',
                'description'       => 'Activiteiten die niet in andere locatiecategorieën passen',
                'category_group_id' => 3,
            ],
            25 => [
                'id'                => 26,
                'is_published'      => 1,
                'name'              => '5-15 min',
                'description'       => 'Korte activiteiten die tussen de 5 en 15 minuten duren',
                'category_group_id' => 4,
            ],
            26 => [
                'id'                => 27,
                'is_published'      => 1,
                'name'              => '15-30 min',
                'description'       => 'Activiteiten die tussen de 15 en 30 minuten duren',
                'category_group_id' => 4,
            ],
            27 => [
                'id'                => 28,
                'is_published'      => 1,
                'name'              => '30 min - 1 uur',
                'description'       => 'Activiteiten die tussen de 30 minuten en een uur duren',
                'category_group_id' => 4,
            ],
            28 => [
                'id'                => 29,
                'is_published'      => 1,
                'name'              => '1-2 uur',
                'description'       => 'Activiteiten die tussen de één en twee uur duren',
                'category_group_id' => 4,
            ],
            29 => [
                'id'                => 30,
                'is_published'      => 1,
                'name'              => '2-3 uur',
                'description'       => 'Activiteiten die tussen de twee en drie uur duren',
                'category_group_id' => 4,
            ],
            30 => [
                'id'                => 31,
                'is_published'      => 1,
                'name'              => 'Halve dag',
                'description'       => 'Activiteiten die een dagdeel (ochtend of middag) duren',
                'category_group_id' => 4,
            ],
            31 => [
                'id'                => 32,
                'is_published'      => 1,
                'name'              => 'Hele dag',
                'description'       => 'Activiteiten die een volledige dag duren',
                'category_group_id' => 4,
            ],
            32 => [
                'id'                => 33,
                'is_published'      => 1,
                'name'              => 'Meerdere dagdelen / opkomsten',
                'description'       => 'Activiteiten die over meerdere bijeenkomsten verspreid zijn',
                'category_group_id' => 4,
            ],
            33 => [
                'id'                => 34,
                'is_published'      => 1,
                'name'              => 'Individueel',
                'description'       => 'Activiteiten die door één persoon kunnen worden uitgevoerd',
                'category_group_id' => 5,
            ],
            34 => [
                'id'                => 35,
                'is_published'      => 1,
                'name'              => '1-8 personen',
                'description'       => 'Activiteiten geschikt voor kleine groepen van 1 tot 8 personen',
                'category_group_id' => 5,
            ],
            35 => [
                'id'                => 36,
                'is_published'      => 1,
                'name'              => '8-15 personen',
                'description'       => 'Activiteiten geschikt voor middelgrote groepen van 8 tot 15 personen',
                'category_group_id' => 5,
            ],
            36 => [
                'id'                => 37,
                'is_published'      => 1,
                'name'              => '15 of meer',
                'description'       => 'Activiteiten geschikt voor groepen van 15 of meer deelnemers',
                'category_group_id' => 5,
            ],
            37 => [
                'id'                => 38,
                'is_published'      => 1,
                'name'              => 'Groepsactiviteit',
                'description'       => 'Activiteiten waarbij de hele scoutinggroep betrokken is',
                'category_group_id' => 5,
            ],
            38 => [
                'id'                => 39,
                'is_published'      => 1,
                'name'              => 'Geen',
                'description'       => 'Activiteiten die geen voorbereidingstijd vereisen',
                'category_group_id' => 6,
            ],
            39 => [
                'id'                => 40,
                'is_published'      => 1,
                'name'              => '5-15 min',
                'description'       => 'Voorbereidingstijd tussen de 5 en 15 minuten',
                'category_group_id' => 6,
            ],
            40 => [
                'id'                => 61,
                'is_published'      => 1,
                'name'              => '15-30 min',
                'description'       => 'Voorbereidingstijd tussen de 15 en 30 minuten',
                'category_group_id' => 6,
            ],
            41 => [
                'id'                => 41,
                'is_published'      => 1,
                'name'              => '30 min - uur',
                'description'       => 'Voorbereidingstijd van 30 minuten tot een uur',
                'category_group_id' => 6,
            ],
            42 => [
                'id'                => 42,
                'is_published'      => 1,
                'name'              => '1-2 uur',
                'description'       => 'Voorbereidingstijd van één tot twee uur',
                'category_group_id' => 6,
            ],
            43 => [
                'id'                => 43,
                'is_published'      => 1,
                'name'              => '2-3 uur',
                'description'       => 'Voorbereidingstijd van twee tot drie uur',
                'category_group_id' => 6,
            ],
            44 => [
                'id'                => 44,
                'is_published'      => 1,
                'name'              => 'Halve dag',
                'description'       => 'Voorbereidingstijd van een halve dag',
                'category_group_id' => 6,
            ],
            45 => [
                'id'                => 45,
                'is_published'      => 1,
                'name'              => 'Hele dag',
                'description'       => 'Voorbereidingstijd van een hele dag',
                'category_group_id' => 6,
            ],
            46 => [
                'id'                => 46,
                'is_published'      => 1,
                'name'              => 'Samenwerken en Verbinden',
                'description'       => 'Activiteiten gericht op teamwork en het versterken van onderlinge banden',
                'category_group_id' => 7,
            ],
            47 => [
                'id'                => 47,
                'is_published'      => 1,
                'name'              => 'Open en Divers',
                'description'       => 'Activiteiten gericht op inclusiviteit en diversiteit',
                'category_group_id' => 7,
            ],
            48 => [
                'id'                => 48,
                'is_published'      => 1,
                'name'              => 'Ontwikkeling en Uitdaging',
                'description'       => 'Activiteiten gericht op persoonlijke groei en het aangaan van uitdagingen',
                'category_group_id' => 7,
            ],
            49 => [
                'id'                => 49,
                'is_published'      => 1,
                'name'              => 'Vrijwilligers',
                'description'       => 'Activiteiten gericht op het ondersteunen en waarderen van vrijwilligers',
                'category_group_id' => 7,
            ],
            50 => [
                'id'                => 50,
                'is_published'      => 1,
                'name'              => 'Trots en Zichtbaar',
                'description'       => 'Activiteiten die scouting positief onder de aandacht brengen',
                'category_group_id' => 7,
            ],
            51 => [
                'id'                => 51,
                'is_published'      => 1,
                'name'              => 'CWO Buitenboordmotor',
                'description'       => 'Kwalificatie voor het varen met een buitenboordmotor volgens CWO-richtlijnen',
                'category_group_id' => 8,
            ],
            52 => [
                'id'                => 52,
                'is_published'      => 1,
                'name'              => 'CWO Kielboot',
                'description'       => 'Kwalificatie voor het zeilen met een kielboot volgens CWO-richtlijnen',
                'category_group_id' => 8,
            ],
            53 => [
                'id'                => 53,
                'is_published'      => 1,
                'name'              => 'CWO Sloep/Motorvlet',
                'description'       => 'Kwalificatie voor het varen met een sloep of motorvlet volgens CWO-richtlijnen',
                'category_group_id' => 8,
            ],
            54 => [
                'id'                => 54,
                'is_published'      => 1,
                'name'              => 'CWO Roeiboot',
                'description'       => 'Kwalificatie voor het varen met een roeiboot volgens CWO-richtlijnen',
                'category_group_id' => 8,
            ],
            55 => [
                'id'                => 55,
                'is_published'      => 1,
                'name'              => 'Development Award',
                'description'       => 'Een award waarmee je meer ontdekt over jezelf, de groep (subgroep of hele speltak) en de wereld om je heen (de maatschappij)',
                'category_group_id' => 9,
            ],
            56 => [
                'id'                => 56,
                'is_published'      => 1,
                'name'              => 'Nature Award',
                'description'       => 'Een award die binnen het activiteitengebied Buitenleven valt. Het is een combinatie van twee bestaande programma’s van de internationale Scoutingorganisaties WAGGGS en WOSM: de Biodiversity challenge badge en het World Scout Environment Programme',
                'category_group_id' => 9,
            ],
            57 => [
                'id'                => 57,
                'is_published'      => 1,
                'name'              => '1 Nacht',
                'description'       => 'Kampen die één dag en één nacht duren',
                'category_group_id' => 10,
            ],
            58 => [
                'id'                => 58,
                'is_published'      => 1,
                'name'              => 'Weekend',
                'description'       => 'Kampen die een weekend (2-3 dagen) duren',
                'category_group_id' => 10,
            ],
            59 => [
                'id'                => 59,
                'is_published'      => 1,
                'name'              => 'Midweek',
                'description'       => 'Kampen die een midweek (4-5 dagen) duren',
                'category_group_id' => 10,
            ],
            60 => [
                'id'                => 60,
                'is_published'      => 1,
                'name'              => 'Week',
                'description'       => 'Kampen die (bijna) een hele week (6-7 dagen) duren',
                'category_group_id' => 10,
            ],
        ]);
    }
}
