@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Nouvelle Dépense</h1>
    <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">Catégorie</label>
            <input name="category" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Description</label>
            <input name="description" class="border p-2 w-full" required>
        </div>
        <div>
            <label class="block">Montant</label>
            <input name="amount" type="number" step="0.01" class="border p-2 w-full" required>
        </div>
        <div>
            <label class="block">Date</label>
            <input name="spent_at" type="date" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Référence</label>
            <input name="reference" class="border p-2 w-full">
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
@endsection
