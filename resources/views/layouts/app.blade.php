<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Stock</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap gap-4 items-center">
            <a href="{{ route('dashboard') }}" class="font-semibold">Accueil</a>
            <a href="{{ route('products.index') }}">Articles</a>
            <a href="{{ route('suppliers.index') }}">Fournisseurs</a>
            <a href="{{ route('customers.index') }}">Clients</a>
            <a href="{{ route('purchases.index') }}">Achats</a>
            <a href="{{ route('stock-transfers.create') }}">Transfert dépôt → magasin</a>
            <a href="{{ route('sales.index') }}">Ventes</a>
            <a href="{{ route('expenses.index') }}">Dépenses</a>
            <a href="{{ route('inventory-counts.create') }}">Inventaire</a>
            <a href="{{ route('reports.financial') }}">Rapports</a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
