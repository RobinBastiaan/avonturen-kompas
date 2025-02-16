<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;

class ThemeUsageController extends Controller
{
    public function __invoke()
    {
        return view('stats.theme-usage', [
            'hotsjietoniaCharacterStats' => $this->getHotsjietoniaCharacterStats(),
            'jungleBookCharacterStats'   => $this->getJungleBookCharacterStats(),
            'jungleBookLocationStats'    => $this->getJungleBookLocationStats(),
        ]);
    }

    protected function getHotsjietoniaCharacterStats(): array
    {
        $characters = [
            'Fleur Kleur', 'Steven Stroom', 'Bas Bos', 'Rozemarijn',
            'Professor Plof', 'Stuiter', 'Sterre', 'Noa', 'Stanley Stekker',
        ];

        return $this->getTermStats($characters);
    }

    protected function getJungleBookCharacterStats(): array
    {
        $characters = [
            'Akela', 'Bagheera', 'Baloe', 'Broer Wolf', 'Chil', 'Hathi',
            'Ikki', 'Jacala', 'Kaa', 'Malchi', 'Mang', 'Marala', 'Mor',
            'Mowgli', 'Oe', 'Raksha', 'Rikki Tikki Tavi', 'Shanti',
            'Shere Khan', 'Tabaqui', 'Vader Wolf',
        ];

        return $this->getTermStats($characters);
    }

    /**
     * These jungle locations match an activity area, and a river for water specific activities.
     */
    protected function getJungleBookLocationStats(): array
    {
        $locations = [
            'Raadsrots', 'Wolvenhol', 'Khaali Jagah-vlakte',
            'Nishaani-plaats', 'Haveli', 'Emaarate',
            'Talaab-poel', 'Guhagrotten', 'Waingunga',
        ];

        return $this->getTermStats($locations);
    }

    /**
     * Get usage statistics for a list of terms by counting their occurrences in item descriptions.
     *
     * @param array $terms List of terms to search for.
     * @return array Array with terms as keys and their occurrence counts as values.
     */
    protected function getTermStats(array $terms): array
    {
        $query = Item::query()
            ->selectRaw('COUNT(*) as count, ? as `term`', [$terms[0]])
            ->whereRaw('description REGEXP ?', ['[[:<:]]' . $terms[0] . '[[:>:]]']);

        foreach (array_slice($terms, 1) as $term) {
            $query->union(
                Item::query()
                    ->selectRaw('COUNT(*) as count, ? as `term`', [$term])
                    ->whereRaw('description REGEXP ?', ['[[:<:]]' . $term . '[[:>:]]'])
            );
        }

        return $query
            ->orderByDesc('count')
            ->toBase()
            ->get()
            ->pluck('count', 'term')
            ->all();
    }
}
