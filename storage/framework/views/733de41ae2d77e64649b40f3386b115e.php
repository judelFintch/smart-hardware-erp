<?php $__env->startSection('content'); ?>
    <h1 class="text-2xl font-semibold mb-4">Tableau de bord</h1>
    <p class="mb-2">Bienvenue dans l'application de gestion de stock.</p>
    <ul class="list-disc pl-6">
        <li>Créer des articles et fournisseurs</li>
        <li>Saisir les achats (locaux ou étrangers)</li>
        <li>Transférer du dépôt vers le magasin</li>
        <li>Enregistrer les ventes (comptant ou crédit)</li>
        <li>Suivre les paiements et les dépenses</li>
    </ul>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/fintch/Desktop/projet/smart-hardware-erp/resources/views/dashboard.blade.php ENDPATH**/ ?>