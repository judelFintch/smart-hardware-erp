@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Modifier Client</h1>
    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block">Nom</label>
            <input name="name" class="border p-2 w-full" value="{{ $customer->name }}" required>
        </div>
        <div>
            <label class="block">Téléphone</label>
            <input name="phone" class="border p-2 w-full" value="{{ $customer->phone }}">
        </div>
        <div>
            <label class="block">Email</label>
            <input name="email" type="email" class="border p-2 w-full" value="{{ $customer->email }}">
        </div>
        <div>
            <label class="block">Adresse</label>
            <input name="address" class="border p-2 w-full" value="{{ $customer->address }}">
        </div>
        <div>
            <label class="block">Notes</label>
            <textarea name="notes" class="border p-2 w-full">{{ $customer->notes }}</textarea>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Mettre à jour</button>
    </form>
@endsection
