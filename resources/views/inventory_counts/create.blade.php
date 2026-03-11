@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Inventaire</h1>
    <form method="POST" action="{{ route('inventory-counts.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block">Lieu</label>
            <select name="location_id" class="border p-2 w-full" required>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block">Date</label>
            <input name="counted_at" type="date" class="border p-2 w-full">
        </div>
        <div>
            <label class="block">Notes</label>
            <textarea name="notes" class="border p-2 w-full"></textarea>
        </div>
        <div>
            <h2 class="text-lg font-semibold mb-2">Articles</h2>
            <div class="space-y-2">
                @foreach (range(0,4) as $index)
                    <div class="grid grid-cols-2 gap-2">
                        <select name="items[{{ $index }}][product_id]" class="border p-2">
                            <option value="">-- Article --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input name="items[{{ $index }}][counted_quantity]" type="number" step="0.001" class="border p-2" placeholder="Quantité comptée">
                    </div>
                @endforeach
            </div>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer inventaire</button>
    </form>
@endsection
