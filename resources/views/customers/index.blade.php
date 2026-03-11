@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Clients</h1>
        <a href="{{ route('customers.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Nouveau</a>
    </div>
    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">Nom</th>
                <th class="p-2">Téléphone</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr class="border-b">
                    <td class="p-2">{{ $customer->name }}</td>
                    <td class="p-2">{{ $customer->phone }}</td>
                    <td class="p-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600">Modifier</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
