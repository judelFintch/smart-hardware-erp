<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Stock</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap gap-4 items-center">
            <a href="<?php echo e(route('dashboard')); ?>" class="font-semibold">Accueil</a>
            <a href="<?php echo e(route('products.index')); ?>">Articles</a>
            <a href="<?php echo e(route('suppliers.index')); ?>">Fournisseurs</a>
            <a href="<?php echo e(route('customers.index')); ?>">Clients</a>
            <a href="<?php echo e(route('purchases.index')); ?>">Achats</a>
            <a href="<?php echo e(route('stock-transfers.create')); ?>">Transfert dépôt → magasin</a>
            <a href="<?php echo e(route('sales.index')); ?>">Ventes</a>
            <a href="<?php echo e(route('expenses.index')); ?>">Dépenses</a>
            <a href="<?php echo e(route('inventory-counts.create')); ?>">Inventaire</a>
            <a href="<?php echo e(route('reports.financial')); ?>">Rapports</a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-6">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>
<?php /**PATH /Users/fintch/Desktop/projet/smart-hardware-erp/resources/views/layouts/app.blade.php ENDPATH**/ ?>