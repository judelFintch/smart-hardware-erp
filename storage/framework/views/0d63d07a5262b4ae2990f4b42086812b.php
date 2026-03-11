<?php $__env->startSection('content'); ?>
    <h1 class="text-2xl font-semibold mb-4">Inventaire</h1>
    <form method="POST" action="<?php echo e(route('inventory-counts.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block">Lieu</label>
            <select name="location_id" class="border p-2 w-full" required>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php $__currentLoopData = range(0,4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="items[<?php echo e($index); ?>][product_id]" class="border p-2">
                            <option value="">-- Article --</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input name="items[<?php echo e($index); ?>][counted_quantity]" type="number" step="0.001" class="border p-2" placeholder="Quantité comptée">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded" type="submit">Enregistrer inventaire</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/fintch/Desktop/projet/smart-hardware-erp/resources/views/inventory_counts/create.blade.php ENDPATH**/ ?>