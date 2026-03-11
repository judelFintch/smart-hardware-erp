@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Modifier Dépense</h1>
    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block">Catégorie</label>
            <input name="category" class="border p-2 w-full" value="{{ $expense->category }}">
        </div>
        <div>
            <label class="block">Description</label>
            <input name="description" class="border p-2 w-full" value="{{ $expense->description }}" required>
        </div>
        <div>
            <label class="block">Montant</label>
            <input name="amount" type="number" step="0.01" class="border p-2 w-full" value="{{ $expense->amount }}" required>
        </div>
        <div>
            <label class="block">Date</label>
            <input name="spent_at" type="date" class="border p-2 w-full" value="{{ $expense->spent_at }}">
        </div>
        <div>
            <label class="block">Référence</label>
            <input name="reference" class="border p-2 w-full" value="{{ $expense->reference }}">
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Mettre à jour</button>
    </form>
@endsection
