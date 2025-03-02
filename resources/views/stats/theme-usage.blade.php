@extends('layouts.app')

@section('main')
    <h1 class="text-3xl font-bold">Thema gebruik</h1>

    <div class="row mt-4">
        <div class="col-md-6">
            <h3 class="text-l font-semibold text-[#F2940A]">Hotsjietonia Karakters</h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Karakter</th>
                    <th>Hoeveel gebruikt</th>
                </tr>
                </thead>
                <tbody>
                @foreach($hotsjietoniaCharacterStats as $term => $count)
                    <tr>
                        <td>{{ $term }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3 class="text-l font-semibold text-[#F2940A]">Jungle Boek Karakters</h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Karakter</th>
                    <th>Hoeveel gebruikt</th>
                </tr>
                </thead>
                <tbody>
                @foreach($jungleBookCharacterStats as $term => $count)
                    <tr>
                        <td>{{ $term }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3 class="text-l font-semibold text-[#F2940A]">Jungle Boek Locaties</h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Locatie</th>
                    <th>Hoeveel gebruikt</th>
                </tr>
                </thead>
                <tbody>
                @foreach($jungleBookLocationStats as $term => $count)
                    <tr>
                        <td>{{ $term }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
