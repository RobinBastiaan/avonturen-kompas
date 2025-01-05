<?php

namespace App\Console\Commands;

use App\Models\AtScout;
use App\Models\ExtractedItem;
use App\Models\Item;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ProcessHistoricalAtScoutsCommand extends Command
{
    protected $signature = 'app:process:historical-at-scouts';
    protected $description = 'Process historical @-scout magazine editions from array, which should be run after items have been processed.';

    public function handle(): int
    {
        if (!Item::exists()) {
            $this->error('Please process items first before running this command.');
            return CommandAlias::FAILURE;
        }

        $startTime = now();

        $historicalAtScoutsCount = $this->processAtScouts();

        $totalTime = ceil(now()->diffInSeconds($startTime, true));
        $this->newLine();
        $this->info("Processed {$historicalAtScoutsCount} historical @-scouts in {$totalTime} seconds.");

        return CommandAlias::SUCCESS;
    }

    /**
     * Find related items from the original ID's, and write them to the AtScout model.
     *
     * @return int The amount of @-scout magazine editions in the data array.
     */
    protected function processAtScouts(): int
    {
        $historicalAtScoutsCount = count(self::HISTORICAL_AT_SCOUTS);
        $progressBar = $this->output->createProgressBar($historicalAtScoutsCount);
        $progressBar->start();

        foreach (self::HISTORICAL_AT_SCOUTS as $atScout) {
            $beversItemId = $this->findItem($atScout[2]);
            $welpenItemId = $this->findItem($atScout[3]);
            $scoutsItemId = $this->findItem($atScout[4]);
            $explorersItemId = $this->findItem($atScout[5]);
            $roverscoutsItemId = $this->findItem($atScout[6]);
            $extraItemId = $this->findItem($atScout[7]);

            // Editions without any items should be skipped to prevent empty rows.
            if ($beversItemId === null && $welpenItemId === null && $scoutsItemId === null && $explorersItemId === null && $roverscoutsItemId === null && $extraItemId === null) {
                $this->error("Skipped @-scout of {$atScout[0]} because no related items were found.");
                continue;
            }

            if ($beversItemId === null && $atScout[2]) {
                $this->warn("Skipped original_id {$atScout[2]} in the @-scout of {$atScout[0]} because the related item was not found.");
            }

            if ($welpenItemId === null && $atScout[3]) {
                $this->warn("Skipped original_id {$atScout[3]} in the @-scout of {$atScout[0]} because the related item was not found.");
            }

            if ($scoutsItemId === null && $atScout[4]) {
                $this->warn("Skipped original_id {$atScout[4]} in the @-scout of {$atScout[0]} because the related item was not found.");
            }

            if ($explorersItemId === null && $atScout[5]) {
                $this->warn("Skipped original_id {$atScout[5]} in the @-scout of {$atScout[0]} because the related item was not found.");
            }

            if ($roverscoutsItemId === null && $atScout[6]) {
                $this->warn("Skipped original_id {$atScout[6]} in the @-scout of {$atScout[0]} because the related item was not found.");
            }

            AtScout::firstOrCreate(
                ['published_at' => $atScout[0]],
                [
                    'name'                => $atScout[1],
                    'bevers_item_id'      => $beversItemId ?? null,
                    'welpen_item_id'      => $welpenItemId ?? null,
                    'scouts_item_id'      => $scoutsItemId ?? null,
                    'explorers_item_id'   => $explorersItemId ?? null,
                    'roverscouts_item_id' => $roverscoutsItemId ?? null,
                    'extra_item_id'       => $extraItemId ?? null,
                ],
            );

            $progressBar->advance();
        }

        $progressBar->finish();

        return $historicalAtScoutsCount;
    }

    protected function findItem(int $originalItemId): ?int
    {
        return ExtractedItem::query()
            ->select('items.id')
            ->join('items', 'items.id', '=', 'extracted_items.applied_to')
            ->where('extracted_items.original_id', $originalItemId)
            ->value('items.id');
    }

    protected const HISTORICAL_AT_SCOUTS = [
        ['2011-03-17', 'Creatief met verf', 91, 126, 238, 449, null, null],
        ['2011-03-31', 'Wie ben ik? Wat vind ik belangrijk? Hoe draag ik bij?', 213, 245, 246, 247, 248, null],
        ['2011-04-14', 'Internationaal', 250, 251, 252, 253, 254, null],
        ['2011-05-12', 'Heerlijk weer', 58, 82, 370, 189, 115, null],
        ['2011-05-26', 'Buitenleven', 228, 29, 284, 285, 288, null],
        ['2011-10-27', 'Halloween ', 367, 368, 369, 369, null, null],
        ['2011-11-24', 'Sinterklaas', 398, 397, 388, 391, 389, null],
        ['2011-12-08', 'Kerst', 402, 404, 401, null, null, null],
        ['2011-12-22', 'Identiteit', 416, 405, 414, 415, 392, null],
        ['2012-01-05', 'Winter', 419, 6302, null, 411, 393, null],
        ['2012-01-19', 'Uitdagende Scoutingtechnieken', 441, 407, 413, 432, null, null],
        ['2012-02-02', 'Valentijnsdag', 468, 409, 465, 450, 394, null],
        ['2012-03-01', 'Hergebruiken van spullen, creatieve dingen mee maken', 487, 494, 488, 484, 482, null],
        ['2012-03-15', 'Samenleving', 500, 502, 499, 498, 497, 483],
        ['2012-09-13', 'Jeugdparticipatie ', 584, 583, 580, 582, 578, null],
        ['2012-11-08', 'Veilig & Gezond', 609, 610, 607, 606, 608, null],
        ['2013-01-31', 'Sterren/sterrenbeelden', 647, 650, 649, null, 648, null],
        ['2013-02-14', 'Vriendschap / Denkdag', 658, 660, 659, 661, 657, null],
        ['2013-02-28', 'The phantom of the polder', 666, 665, 662, 663, 664, null],
        ['2013-03-14', 'Brazillie', 670, 672, 668, 669, 671, null],
        ['2013-04-11', 'Schimmels', 695, 696, 691, 693, 697, null],
        ['2013-04-25', 'Identiteit', 210, 707, 702, 703, 704, null],
        ['2013-05-09', 'Spelletjes uit moeders tijd', 713, 714, 370, 711, 712, null],
        ['2013-05-23', 'Indianen', 719, 718, 715, 716, 717, null],
        ['2013-06-06', 'Bandconcours', 743, 742, 740, 744, 741, null],
        ['2013-07-04', 'Lucht', 746, 749, 748, 750, 747, null],
        ['2013-08-29', 'Kennismaken (overvliegen)', 783, 780, 294, 2058, 781, null],
        ['2013-09-12', 'Water', 785, 269, 784, 793, 786, null],
        ['2013-09-26', 'WSJ2015 Japan', 806, 808, 805, 807, 804, null],
        ['2013-10-10', 'Melk', 811, 815, 813, 814, 812, null],
        ['2013-10-24', 'Pionieren', 113, 819, 818, 6362, 816, null],
        ['2013-11-07', 'EHBO', 824, 827, 826, 399, 789, null],
        ['2013-11-21', 'Kookfeest', 832, 833, 636, 633, 831, null],
        ['2013-12-05', 'Herman van Veen', 841, 844, 843, 842, 838, null],
        ['2013-12-19', 'Wintersport', 848, 159, 418, 847, 846, 419],
        ['2014-01-02', 'Dieren in de winter', 853, 852, 851, 849, 850, null],
        ['2014-01-16', 'Codes', 857, 2292, 855, 451, 856, null],
        ['2014-01-30', 'Partnership Ghana + Schoenmaatjes met World Thinking Day', 861, 862, 863, 864, 377, null],
        ['2014-02-13', 'Internationale vrouwendag (8 maart)(+ Valentijnsdag)', 865, 868, 870, 866, 867, 869],
        ['2014-02-27', 'Week van het geld', 873, 875, 874, 424, 872, null],
        ['2014-03-13', 'Verkeer', 879, 877, 880, 881, 878, null],
        ['2014-03-27', 'Kaart & Kompas', 884, 136, 882, 885, 883, null],
        ['2014-04-10', 'St. Joris', 890, 889, 888, 892, 889, null],
        ['2014-04-24', 'Messengers of Peace', null, null, null, null, null, 896],
        ['2014-05-22', 'Echt Hollands', 905, 651, 912, 911, 904, null],
        ['2014-06-05', 'Buitenspeeldag Uit de computer', 917, 915, 913, 916, 914, null],
        ['2014-06-19', 'Mijn familie', 923, 921, 920, 922, 919, null],
        ['2014-07-03', 'Op kamp gaan', 927, 81, 40, 926, 925, null],
        ['2014-08-28', 'Petfles', 989, 987, 988, 986, 985, null],
        ['2014-09-11', 'JOTA-JOTI After Daylight', 647, 710, 855, 842, 885, null],
        ['2014-09-25', 'Landelijke Scouting Zeilwedstrijden (LSZW)', 993, 471, 466, 18, 992, null],
        ['2014-10-09', 'Halloween', 1000, 999, 596, 994, 996, null],
        ['2014-10-23', 'Natuurwerkdag', 1001, 1003, 579, 1002, 1002, null],
        ['2014-11-20', 'Super Scouting Sinttips', 1028, 1028, 1026, 1026, 1026, null],
        ['2014-12-04', 'Persoonskenmerken en kwaliteiten van zichzelf en hun groepsgenoten', 1034, 1035, 1038, 1036, 1037, null],
        ['2014-12-18', 'Expressie', 1065, 1066, 1064, 1067, 495, null],
        ['2015-01-01', 'Een gelukkig en uitdagend 2015!', 346, 1076, 1078, 705, 1077, null],
        ['2015-01-15', 'Het Dorp van Wim Sonneveld', 1039, 1099, 1106, 1098, 1079, null],
        ['2015-01-29', 'Chinees nieuwjaar', 1109, 1110, 1111, 1108, 1107, null],
        ['2015-02-12', 'Vuurtjes stoken', 1124, 1126, 1129, 1125, 1112, null],
        ['2015-02-26', 'Ridderlijke spelletjes', 1136, 1135, 1135, 1138, 1137, null],
        ['2015-03-12', 'Leven op een boerderij', 1145, 1143, 1146, 1144, 1142, null],
        ['2015-03-26', 'Pasen', 1149, 1148, 1151, 1150, 1147, null],
        ['2015-04-09', 'Koningsdag Argentinië Koningin Maxima', 1164, 1162, 1163, 1161, 1160, null],
        ['2015-04-23', 'Bevrijdingsdag Onvoltooid bevrijd', 1169, 1168, 1172, 1170, 1165, 1139],
        ['2015-05-07', 'Kaartlezen', 1176, 1175, 597, 1174, 1173, null],
        ['2015-05-21', 'Op reis!', 1113, 936, 933, 918, 438, null],
        ['2015-06-04', 'Circus', 1184, 1181, 1185, 1182, 1179, null],
        ['2015-06-18', '#Scouting2025', 1195, 1183, 1196, 1194, 1191, null],
        ['2015-07-02', 'Sahara', 1208, 1207, 1211, 492, 1201, null],
        ['2015-09-10', 'Kinderliedjes', 2052, 2053, 1040, 2049, 2050, null],
        ['2015-09-24', 'De Toekomstboom', 2067, 29, 2068, 2070, 2071, null],
        ['2015-10-08', 'Kinderboekenweek Raar Maar Waar', 427, 2085, 2084, 2074, 2078, null],
        ['2015-10-22', 'Duurzame vrede Vluchtelingen', 2102, 2101, 2103, 2059, 2059, null],
        ['2015-11-05', 'WereldWaterWeek Water voor ontwikkeling', 2109, 2108, 2111, 499, null, null],
        ['2015-11-19', 'Sinterklaas', 2125, 2117, 2120, 2115, 2116, null],
        ['2015-12-03', 'Sinterklaasspelletjes uit de oude doos!', 832, 397, 1212, 1026, 1030, null],
        ['2015-12-17', 'Vredeslicht', 2140, 2141, 2144, 390, 2145, null],
        ['2015-12-31', 'Nieuwjaarsreceptie', 2147, 2142, 2149, 2150, 2146, null],
        ['2016-01-14', 'Dag van de Religie', 2157, 2155, 777, 2156, 2154, null],
        ['2016-01-28', 'World Thinking Day Connect', 2167, 2168, 2172, 2171, 2166, null],
        ['2016-02-11', 'Trots & Zichtbaar (#scouting2025)', null, 2165, 560, 602, 2176, null],
        ['2016-02-25', 'Nationale Complimentendag', 2186, 2184, 493, 2190, 2188, null],
        ['2016-03-10', 'Holi Festival', 2201, 2203, 2196, 2204, 2200, 2202],
        ['2016-03-24', 'Disneyliedjes', 2215, 2211, 2209, 2213, 2210, 2212],
        ['2016-04-07', 'Leonardo da Vinci', 2220, 2219, 2216, 2221, 2218, null],
        ['2016-04-21', 'Groot spel voor kleine groep', 2229, 2226, 2223, 2228, 781, null],
        ['2016-05-05', 'Heel Holland bakt', 2234, 2231, 2232, 2230, 2235, null],
        ['2016-06-02', 'Waterwerk Drakenboot', 2248, 2246, 2238, 2247, 2245, null],
        ['2016-06-16', 'Midzomernacht', 2256, 2252, 2254, 2257, 2255, null],
        ['2016-06-30', 'Rio', 2267, 2264, 2258, 2266, 2268, null],
        ['2016-08-25', 'Kwaliteitstest', 2287, 2319, 2317, 2323, 2325, null],
        ['2016-09-08', 'Monumentendag', 2293, 2290, 2289, 2294, 2291, null],
        ['2016-09-22', 'Kampthema', 2309, 2286, 2308, null, 2268, null],
        ['2016-10-20', 'Eerlijkheid', 2337, 2324, 2334, 2321, 1152, null],
        ['2017-01-26', 'Zeven wereldwonderen', 2399, 2397, 2400, 2401, 2402, null],
        ['2017-02-09', 'Route technieken', 2395, 2398, 2404, 2409, 2416, null],
        ['2017-02-23', 'Spelen uit het buitenland', 2403, 2415, 2065, 2408, 2414, null],
        ['2017-03-09', 'Verkiezingen', 2396, 2412, 2406, null, 2418, null],
        ['2017-03-23', 'Aarde / compost', 2411, 2407, 2413, 2417, 2419, null],
        ['2017-10-19', 'Luchtscouting', null, 6214, 6212, 6213, null, null],
        ['2017-11-16', 'Ten minste houdbaar tot', 6228, 6231, 6217, 6237, 6229, null],
        ['2017-11-30', 'Heemkunde', null, 6251, 6256, null, 6249, null],
        ['2017-12-07', 'Exotische kerst', null, 6272, 6304, 6263, null, null],
        ['2018-01-04', 'Nawaka', 6257, 6255, 6302, 6289, 6296, null],
        ['2018-01-18', 'Tekenfilms', 6280, 6250, 6244, 6264, 6295, null],
        ['2018-02-01', 'Olympische Winterspelen', 6248, 6262, 6298, 6265, 6260, null],
        ['2018-02-15', 'World Thinking Day', 6284, 6276, 6294, 6330, 6329, null],
        ['2018-03-01', 'De Paralympische spelen', 6301, 6271, 6246, 972, 6285, null],
        ['2018-03-15', 'Voorjaarsspeltips', 6300, 6254, 502, 6273, 6277, null],
        ['2018-03-29', 'Internationale dag van het kinderboek', 6293, 6253, 6267, 6269, 6282, null],
        ['2018-04-12', 'Dag tegen Pesten', 6374, 6274, 6258, 6268, 6376, null],
        ['2018-04-26', 'Persvrijheid op social media', 6305, 6409, 6281, 6292, 2321, null],
        ['2018-05-10', 'Dag van de popmuziek', 6366, 6360, 6358, 6369, 6365, null],
        ['2018-05-24', 'Sierknopen en schiemannen', 6361, 6372, 6354, 6362, 6351, null],
        ['2018-06-07', 'Suikerfeest', 6349, 6352, 6356, 6363, 6369, null],
        ['2018-06-21', 'Water', 6949, 6930, 6934, 6929, 6931, null],
        ['2018-07-05', 'Kwaku Zomerfestival', 6390, 6387, 6370, 6359, 6350, null],
        ['2018-07-19', 'Scout Scarf Day', 6385, 6386, 6377, 6381, 6379, null],
        ['2018-08-02', 'Kamperen in de regen', 6373, 6357, 6367, 6957, 6348, null],
        ['2018-08-16', 'Internationale Dag van de Fotografie', 6395, 6404, 6403, 6400, 6394, null],
        ['2018-08-30', 'Gewoontes en tradities in Nederland', 6388, 6393, 6398, 6389, 6399, null],
        ['2018-09-13', 'Ouder & Kindopkomst', 1242, 1243, 1244, 582, 1235, null],
        ['2018-09-27', 'Coming Out Dag', 6392, 6397, 2081, 2083, 6402, null],
        ['2018-10-11', 'JOTA-JOTI Expeditie Cosmos', 6979, 6980, 2132, 6107, 1235, null],
        ['2018-11-08', 'Dag van de wetenschap voor vrede en ontwikkeling', 6996, 6997, 2173, 797, 7001, 6999],
        ['2018-11-24', 'Nationale Vrijwilligersdag', 7014, 7012, 7013, 7010, 7015, null],
        ['2018-12-06', 'Midwinteropkomst', 7018, 7019, 7021, 7016, 7017, null],
        ['2018-12-20', 'Eerste of laatste opkomst van het jaar', 7032, 7031, 7026, 263, null, null],
        ['2019-01-10', 'Het jaar van Rembrandt van Rijn', 7035, 7036, 7037, 7038, 7039, null],
        ['2019-01-24', 'Douanedag', 7025, 7027, 7024, 7022, 7023, null],
        ['2019-02-07', 'Vrouwen in de wetenschap', 7034, 6997, 7029, 7028, 7030, null],
        ['2019-02-21', 'World Thinking Day', null, null, null, null, null, 7153],
        ['2019-03-07', 'Zero Discrimination Day', 7063, 945, 7066, 7190, 1036, null],
        ['2019-03-21', 'All about the money: tips voor groep en speltips', 7232, 7205, 7241, 443, 7238, 7244],
        ['2019-04-04', 'Internationale Dag van de Ruimtevaart', 7298, 7301, 7307, 107, 108, null],
        ['2019-04-18', 'S van Samen', 7385, 2462, 7397, 7379, 7412, null],
        ['2019-05-02', 'C van Code', 7349, 7355, 7370, 7394, 7391, null],
        ['2019-05-16', 'O van Outdoor', 7430, 7460, 7451, null, 7445, null],
        ['2019-05-30', 'U van Uitdaging', 7421, 7454, 7463, 7532, 7436, null],
        ['2019-06-13', 'T van Team', 7400, 7361, 7382, 7373, 7364, null],
        ['2019-06-27', 'S van Spel', 7406, 7409, 7418, 7427, 7448, null],
        ['2019-07-11', 'WSJ2019 Amerika', 7580, 7592, 7627, 7574, 7571, null],
        ['2019-07-25', 'Internationale dag van de Natuurbescherming', 1043, 7717, 7711, 7669, 7705, null],
        ['2019-08-08', 'Ongezonde dingen', 7714, 7699, 7708, 7696, 7693, null],
        ['2019-08-22', 'Participatieladder', 7871, 7874, 7880, 7883, 7922, null],
        ['2019-09-05', 'Moderne Route Technieken', 2167, 7859, 7618, 315, 871, null],
        ['2019-09-19', 'Toekomstvisie - Scouting Groeit', 7415, 7958, 7964, 7439, 7358, 8133],
        ['2019-10-03', 'Scout-In', 8010, 8031, 8046, 8082, 8097, 8127],
        ['2019-10-17', 'Diversiteit', 8213, 8205, 8210, 8225, 8207, null],
        ['2019-10-31', 'Vrijwilligers', 8647, 583, 8656, 8650, 8653, null],
        ['2019-11-14', 'Trots en zichtbaarheid', 8647, 583, 8656, 8650, 8653, null],
        ['2019-11-28', 'Ontwikkeling en uitdaging', 8866, 8860, 8878, 8929, 8938, null],
        ['2019-12-12', 'Samenwerken, wees geen eiland, maar bouw bruggen!', 8857, 8851, 8848, 8854, 8998, 8935],
        ['2019-12-26', 'Gezellig samen eten', 8932, 8917, 8893, 8944, 8911, 8920],
        ['2020-01-09', 'Internationale Dag van de Sneeuw', 8887, 8884, 8875, 8881, 8872, null],
        ['2020-01-23', 'Chinees Nieuwjaar', 8926, 9059, 8923, 8902, 8956, null],
        ['2020-02-06', 'Natuur in de winter', 8196, 8785, 8689, 285, 2071, null],
        ['2020-02-20', 'World Thinking Day', null, null, null, null, null, 9176],
        ['2020-03-05', 'Week zonder vlees', 8863, 8869, 8890, 8896, 8899, null],
        ['2020-03-19', 'Thuisopdrachten', 9278, 9272, 9281, 9284, 9269, null],
        ['2020-04-02', 'Stay @ Home', 9341, 9329, 9338, 9374, 9344, null],
        ['2020-04-16', 'Vrijheid', 8950, 8959, 9406, 259, 8953, null],
        ['2020-04-30', 'Moederdag', 9607, 9604, 9616, 9610, 9619, null],
        ['2020-05-14', '1,5 spellen', 9589, 9409, 10045, 2071, 9466, null],
        ['2020-05-28', 'Online opendag', 9613, 9622, 9625, 9628, 9631, null],
        ['2020-05-28', 'Offline opendag', 10170, 9634, 9646, 9694, 9685, null],
        ['2020-06-11', 'Met elkaar voor elkaar', 9475, 8306, 9263, 9317, 9293, null],
        ['2020-06-11', 'Sportief uitleven in beweegspelen', 7280, 10266, 10239, 10090, 293, null],
        ['2020-06-25', 'Goede PR', 9670, 9679, 9658, 9667, 9673, null],
        ['2020-06-25', 'Leden vertellen verhalen', 10093, 10212, 9556, 10036, 10033, null],
        ['2020-07-09', 'Rustig momentje op kamp', 9287, 125, 1192, 758, 10215, null],
        ['2020-07-09', 'Natuur in huis', 10152, 10027, 9496, 9508, 9487, null],
        ['2020-07-23', 'Sunrise day', 6126, 10278, 6148, 9640, 6149, null],
        ['2020-07-23', 'Creatief over het internet', 6201, 9257, 239, 9245, 8112, null],
        ['2020-08-06', 'Geaardheid en vooroordelen', 9013, 9001, 9007, 8225, 964, null],
        ['2020-08-06', 'Stoepkrijt challenge', 9583, 10236, 10197, 9586, 10260, null],
        ['2020-08-20', 'SCENES', 9661, 9655, 9691, 9577, 9676, null],
        ['2020-09-03', 'Waterscouts', 1187, 10626, 10629, 6931, 10632, null],
        ['2020-09-17', 'Kennismakingsspellen', 10677, 10680, 10683, 10686, 10689, null],
        ['2020-10-01', 'JOTI/JOTA', 10755, 10758, 10767, 10764, 520, null],
        ['2020-10-15', 'Maand van de Geschiedenis', 10848, 10923, 10851, 10854, 10857, null],
        ['2020-10-29', 'Herfst en Oogst', 10932, 10953, 10959, 10962, 10962, 10965],
        ['2020-11-12', 'Online Sinterklaas', 446, 830, 10990, 1026, 2116, 11006],
        ['2020-11-26', 'Veilig Verkeer', 11090, 11120, 11081, 11084, 2132, null],
        ['2020-12-10', 'Kerst tijdens Corona', 11117, 11102, 11159, 11087, 11108, null],
        ['2020-12-24', 'Duurzame ontwikkelingsdoelen', 2337, 327, 11177, 11165, 11162, null],
        ['2021-01-07', 'Experimenten met alledaagse voorwerpen', 11105, 11078, 11114, 11206, 793, null],
        ['2021-01-21', 'Fikkie stoken', 11075, 11093, 11099, 11233, 11096, null],
        ['2021-02-04', 'Valentijn; houden van elkaar en jezelf', 11320, 11308, 11311, 11317, 11314, null],
        ['2021-02-18', 'Scouts op het water', 11452, 11449, 11347, 11458, 11455, null],
        ['2021-03-04', 'The Incredible Machine', 11353, 11359, 11344, 11350, 11368, null],
        ['2021-03-18', 'Wereld Jamboree Korea', 11377, 11581, 11362, 11356, 11380, null],
        ['2021-04-01', 'We gaan weer naar buiten!', 11605, 11608, 11374, 11383, 11566, null],
        ['2021-04-15', 'EHBO rond het clubhuis', 11392, 11398, 11401, 11407, 11401, null],
        ['2021-04-29', 'Naar buiten', 1186, 11371, 11749, 11395, 11410, null],
        ['2021-05-13', 'Zintuigen', 11728, 11734, 2133, 11737, 8812, null],
        ['2021-05-27', 'Kinderrechten', 11713, 2087, 11707, 11710, 11725, null],
        ['2021-06-10', 'Speurtochten', 11716, 11731, 11722, 11779, 11873, null],
        ['2021-06-24', 'Internationale sporten', 563, 10893, 1244, 11924, 11936, null],
        ['2021-07-08', 'JOTA', 11957, 11960, 10338, 11966, 1220, null],
        ['2021-07-22', 'Zomerkamp', 2194, 886, 10209, 2228, 11749, null],
        ['2021-08-05', 'Luchtscouting', 11860, 748, 11153, 11135, 11156, null],
        ['2021-08-19', 'Tijd voor Avontuur! Escape room', 10914, 10860, 10368, 9568, 8905, null],
        ['2021-08-20', 'Tijd voor Avontuur! Geldinzamelingselement.', 7232, 12035, 7241, 443, 7244, null],
        ['2021-09-02', '10 jaar activiteitenbank', 6388, 494, 9338, 1026, 596, null],
        ['2021-09-03', '10 jaar activiteitenbank', 1043, 6231, 2133, 2049, 1152, null],
        ['2021-09-16', 'Installeren', 12084, 12087, 12078, 12081, 12075, null],
        ['2021-09-30', 'Ik ben wie ik ben', 12147, 12144, 12129, 12141, 12108, null],
        ['2021-10-14', 'Tijd voor Avontuur en de innerlijke Freek Vonk', 12090, 12093, 12186, 12194, 12183, null],
        ['2021-10-28', 'Nacht van Nacht', 12203, 12221, 12218, 12206, 12209, null],
        ['2021-11-11', 'Creatief in een ander werelddeel', 267, 12017, 12263, 12014, 10329, 10464],
        ['2021-11-25', 'Sint Pannekoek', 12126, 634, 2185, 440, 10413, null],
        ['2021-12-09', 'Vredeslicht', 12245, 12251, 12368, 12347, null, null],
        ['2021-12-23', 'Hugo de groot jaar', 12293, 12326, 12317, 12335, 12341, null],
        ['2022-01-06', 'Dag van de 11 stedentocht', 12287, 12290, 12281, 12278, 12284, null],
        ['2022-01-20', 'Waterscouts in de winter', 857, 8276, 11434, 17, 11455, null],
        ['2022-02-03', 'Dag van alarmnummer 112', 12320, 762, 12314, 12302, 9293, null],
        ['2022-02-17', 'Thinking day / denkdag', 12311, 11632, 11236, 12454, 9532, null],
        ['2022-03-03', 'Week van het geld / Digitaal', 12296, 12329, 12299, 12464, 12465, null],
        ['2022-03-17', 'Earth Hour', 9299, 910, 6211, 9332, 9302, null],
        ['2022-03-17', 'Oekraïne: Aandacht voor oorlog en conflict in jouw speltak', 2377, 2101, 2114, 954, null, null],
        ['2022-03-31', 'Nationale Museumweek', 12433, 12493, 12429, 12437, 12430, null],
        ['2022-04-14', 'Textielhergebruik ', 12503, 12504, 12505, 12506, 12436, null],
        ['2022-04-28', 'Bevrijdingsdag: verbinden', 12434, 12432, 12507, 12508, 12515, null],
        ['2022-05-12', 'Dag van Europa', 12499, 12546, 12544, 12548, 12547, null],
        ['2022-05-26', 'Fietsen', 12502, 10030, 12509, 12498, 12501, 12500],
        ['2022-06-09', 'Buitenspelen / Open dagen', 1178, 1180, 942, 549, 371, null],
        ['2022-06-23', 'Dag van de afkoeling', 12586, 11497, 12587, 12584, 6237, null],
        ['2022-07-07', 'Rubiks kubus; Escape Rooms', 10914, 10860, 6190, 9167, 9577, 12117],
        ['2022-07-21', 'World Neckerday Internationaal', 12623, 12625, 12624, 12627, 12626, null],
        ['2022-08-04', 'Binnenvaart Insigne', 857, 11960, 11, 6929, 12424, null],
        ['2022-08-18', 'Buitenleven!', 8641, 8707, 2417, 12257, 693, null],
        ['2022-09-01', 'Duurzame Dinsdag', 12681, 12682, 12702, 12680, 12684, null],
        ['2022-09-15', 'Dovendag / Dag van de gebarentaal', 11728, 12732, 12737, 12731, 12743, null],
        ['2022-09-29', 'Kennismaking', 923, 12144, 920, 8944, 919, null],
        ['2022-10-13', 'Brand', 12734, 12733, 12789, 12738, 12744, null],
        ['2022-10-27', '70 Jaar Donald Duck', 12824, 12736, 12746, 12745, 12735, null],
        ['2022-11-10', 'Dag van de Uitvinders (9 november) ', 12759, 9664, 12834, 12741, 12739, null],
        ['2022-11-24', 'Zwitserland', 12872, 12873, 12874, 12875, 6296, null],
        ['2022-12-08', 'Paarse Vrijdag', 9013, 12831, 9007, 8225, 9001, null],
        ['2022-12-22', 'Luchtscouts: Copernicus vliegtuigen', 12859, 6997, 12856, 12854, 12870, null],
        ['2023-01-05', 'Nieuw jaar', 12851, 12886, 12850, 12848, 12866, null],
        ['2023-01-19', 'Hand lettering', 12845, 12868, 12858, 12853, 8473, null],
        ['2023-02-02', 'Warmetruienweekend', 12849, 12857, 12862, 12865, 12871, 12864],
        ['2023-02-16', 'Regenboog Valentijn', 468, 11308, 8225, 394, 866, null],
        ['2023-03-02', 'Digitale veiligheid', 12847, 12843, 12844, 12846, 1152, null],
        ['2023-03-16', 'Pi day', 12860, 12869, 12861, 12867, 12863, null],
        ['2023-03-30', 'Hanzesteden', 12908, 12907, 12909, 12902, 12905, null],
        ['2023-04-13', 'Circus', 12900, 7397, 12901, 12906, 12899, null],
        ['2023-04-27', '4-5 mei: Leven met oorlog ', 257, 10317, 12946, 8953, 1170, null],
        ['2023-05-09', 'Nationale Molen- & Gemalendag', 12911, 12914, 12913, 12915, 12917, null],
        ['2023-05-23', 'Vogalonga ', 12910, 12964, 21, 12912, 12916, null],
        ['2023-06-06', 'Dag van de Slang', 12954, 8166, 12951, 12952, 12955, null],
        ['2023-06-20', 'Keti Koti', 12950, 11975, 970, 12949, 12948, null],
        ['2023-07-04', 'Fietsen', 12947, 12509, 12953, 12501, 11084, null],
        ['2023-07-18', 'Gay Pride', 12977, 13055, 13070, 13057, 13056, null],
        ['2023-08-01', 'Sneekweek', 13072, 8440, 12965, 13074, 13075, null],
        ['2023-08-15', 'Fotografie', 10428, 7654, 12693, 821, 2166, null],
        ['2023-08-29', 'Hiken met topografische kaart', 12959, 9121, 12945, 12961, 12962, 8273],
        ['2023-09-12', 'Magie', 13103, 13089, 13093, 13092, 13088, '^'],
        ['2023-09-26', 'Soekot of Loofhuttenfeest', 13091, 10305, 13090, 13087, 13086, null],
        ['2023-10-10', 'JOTA-JOTI: Connecting Dots', 13129, 714, 11845, 13128, 13124, null],
        ['2023-10-24', 'Nacht van de Nacht', 13094, 13095, 13096, 13098, 13099, null],
        ['2023-11-07', 'Dag van de vriendelijkheid', 13108, 13102, 13097, 13101, 13100, null],
        ['2023-11-21', 'Midwinter', 13155, 13157, 390, 13149, 13153, null],
        ['2023-12-05', 'Kerst', 11212, 11209, 11215, 8833, 8830, null],
        ['2023-12-19', 'Oud en Nieuw', 13178, 11614, 13159, 13156, 13154, null],
        ['2024-01-02', 'Kringloop en hergebruik', 13161, 13181, 909, 13160, 13164, null],
        ['2024-01-16', 'Tropische activiteiten', 13179, 13165, 13163, 13162, 705, null],
        ['2024-01-30', 'World Thinking Day', 13214, 13213, 13218, 13216, 13217, null],
        ['2024-02-13', 'Pionieren', 13187, 13166, 13167, 13168, 13169, null],
        ['2024-02-27', 'Week van het geld', 13219, 874, 11165, 13230, 381, null],
        ['2024-03-12', 'Buiten op- en rond het water', 857, 565, 161, 13240, 13241, null],
        ['2024-03-26', 'Pasen', 506, 505, 8755, 510, 1150, null],
        ['2024-04-09', 'Vrijheid', 13294, 1168, 1139, 12946, null, null],
        ['2024-04-23', 'Bloemen', 13174, 13175, 13180, 9589, 7946, null],
        ['2024-05-07', 'Insigne Internationaal', 541, 651, 252, 13275, 13279, null],
        ['2024-05-21', 'Freek Vonk', 12883, 12833, 12611, 7556, 9676, null],
        ['2024-06-04', 'Insigne Identiteit', 621, 551, 292, 9221, 11236, null],
        ['2024-06-18', 'Verven', 13321, 13322, 13326, 13327, 13324, null],
        ['2024-07-02', 'Terug van vakantie', 13303, 13306, 13307, 13310, 13317, null],
        ['2024-07-16', 'Insigne Samenleving', 265, 6139, 574, 2266, 6329, null],
        ['2024-07-30', 'Bear Grylls', 13313, 10305, 13304, 13316, 13302, null],
        ['2024-08-13', 'Nieuwe Scoutingseizoen', 13312, 13305, 13320, 13309, 13308, null],
        ['2024-08-27', 'Insigne Veilig & Gezond', 215, 13236, 13237, 13323, 13329, null],
        ['2024-09-10', "Kampthema's", 2296, 13331, 2192, 2060, 184, 13330],
        ['2024-09-24', 'Unieke routetechnieken', 13314, 13315, 13311, 13341, 13340, null],
        ['2024-10-08', 'Maand van de geschiedenis', 13386, 13393, 13383, 13394, 13381, null],
        ['2024-10-22', 'Wereld vrijheid dag ', 13389, 13402, 13395, 13387, 13390, null],
        ['2024-11-05', 'Internationale Dag van de Religieuze Vrijheid ', 13382, 13384, 13385, 13388, 11936, null],
        ['2024-11-19', 'Dag van de muzikanten', 13439, 13438, 13441, 13391, 13440, null],
        ['2024-12-03', 'Internationale dag van de burgerluchtvaart', 13450, 13455, 13454, 13461, 13449, null],
        ['2024-12-17', 'Internationale dag van de korte film ', 13456, 13451, 13448, 13453, 13452, null],
        ['2024-12-31', 'Braille dag', 13460, 13475, 13477, 13463, 13476, null],
    ];
}
