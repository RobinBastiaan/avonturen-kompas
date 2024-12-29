@extends('layouts.app')

@section('main')
    <h1 class="text-3xl font-bold">Totaal activiteiten</h1>

    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle">
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                        Activiteitengebied
                    </th>
                    @foreach($stats->first() as $ageGroup => $count)
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            {{ preg_replace('/^\d+-\d+\s/', '', $ageGroup) }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($stats as $activityArea => $ageGroups)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 text-center">
                            {{ $activityArea }}
                        </td>
                        @foreach($stats->first() as $ageGroup => $count)
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">
                                {{ $ageGroups[$ageGroup] ?? 0 }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
