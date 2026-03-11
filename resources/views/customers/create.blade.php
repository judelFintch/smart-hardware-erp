@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Nouveau Client</h1>
    <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">Nom</label>
            <input name="name" class="border p-2 w-full" required>
        </div>
        <div>
            <label class="block">Téléphone</label>
            <input name="phone" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Email</label>
            <input name="email" type="email" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Adresse</label>
            <input name="address" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Notes</label>
            <textarea name="notes" class="border p-2 w-full"></textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer</button>
    </form>
@endsection
