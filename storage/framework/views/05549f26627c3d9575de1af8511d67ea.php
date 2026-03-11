<?php $__env->startSection('content'); ?>
    <h1 class="text-2xl font-semibold mb-4">Rapport financier</h1>

    <form method="GET" action="<?php echo e(route('reports.financial')); ?>" class="grid grid-cols-3 gap-2 mb-6">
        <input name="start" type="date" class="border p-2" value="<?php echo e($start); ?>">
        <input name="end" type="date" class="border p-2" value="<?php echo e($end); ?>">
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Filtrer</button>
    </form>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow rounded p-4">
            <p><strong>Ventes:</strong> <?php echo e(number_format($salesTotal, 2)); ?></p>
            <p><strong>Coût d'achat vendu:</strong> <?php echo e(number_format($cogsTotal, 2)); ?></p>
            <p><strong>Dépenses:</strong> <?php echo e(number_format($expensesTotal, 2)); ?></p>
            <p><strong>Bénéfice:</strong> <?php echo e(number_format($profit, 2)); ?></p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <p><strong>Crédit restant:</strong> <?php echo e(number_format($creditOutstanding, 2)); ?></p>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-2">Stocks par lieu</h2>
    <?php $__currentLoopData = $stockByLocation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white shadow rounded p-4 mb-4">
            <h3 class="font-semibold mb-2"><?php echo e($entry['location']->name); ?></h3>
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Article</th>
                        <th class="p-2">Quantité</th>
                        <th class="p-2">Coût moyen</th>
                        <th class="p-2">Prix vente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $entry['balances']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo e($balance->product->name); ?></td>
                            <td class="p-2"><?php echo e($balance->quantity); ?></td>
                            <td class="p-2"><?php echo e(number_format($balance->avg_cost_local, 2)); ?></td>
                            <td class="p-2"><?php echo e(number_format($balance->sale_price_local, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/fintch/Desktop/projet/smart-hardware-erp/resources/views/reports/financial.blade.php ENDPATH**/ ?>