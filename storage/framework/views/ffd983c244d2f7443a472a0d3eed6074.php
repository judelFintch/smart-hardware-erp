<?php $__env->startSection('content'); ?>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Clients</h1>
        <a href="<?php echo e(route('customers.create')); ?>" class="px-3 py-2 bg-blue-600 text-white rounded">Nouveau</a>
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
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b">
                    <td class="p-2"><?php echo e($customer->name); ?></td>
                    <td class="p-2"><?php echo e($customer->phone); ?></td>
                    <td class="p-2">
                        <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="text-blue-600">Modifier</a>
                        <form action="<?php echo e(route('customers.destroy', $customer)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="text-red-600" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/fintch/Desktop/projet/smart-hardware-erp/resources/views/customers/index.blade.php ENDPATH**/ ?>