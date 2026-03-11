@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Modifier Fournisseur</h1>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block">Nom</label>
            <input name="name" class="border p-2 w-full" value="{{ $supplier->name }}" required>
        </div>
        <div>
            <label class="block">Type</label>
            <select name="type" class="border p-2 w-full" required>
                <option value="local" @selected($supplier->type === 'local')>Local</option>
                <option value="foreign" @selected($supplier->type === 'foreign')>Étranger</option>
            </select>
        </div>
        <div>
            <label class="block">Téléphone</label>
            <input name="phone" class="border p-2 w-full" value="{{ $supplier->phone }}">
        </div>
        <div>
            <label class="block">Email</label>
            <input name="email" type="email" class="border p-2 w-full" value="{{ $supplier->email }}">
        </div>
        <div>
            <label class="block">Adresse</label>
            <input name="address" class="border p-2 w-full" value="{{ $supplier->address }}">
        </div>
        <div>
            <label class="block">Notes</label>
            <textarea name="notes" class="border p-2 w-full">{{ $supplier->notes }}</textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Mettre à jour</button>
    </form>
@endsection
