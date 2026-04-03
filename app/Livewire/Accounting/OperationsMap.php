<?php

namespace App\Livewire\Accounting;

use App\Models\AccountingSetting;
use Livewire\Component;

class OperationsMap extends Component
{
    public function render()
    {
        $settings = AccountingSetting::query()
            ->with(['account', 'journal'])
            ->get()
            ->keyBy('key');

        $operations = [
            [
                'title' => 'Vente comptant',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'La vente comptant genere un encaissement immediat, un produit de vente, un cout des ventes et une sortie de stock.',
                'journal' => $settings->get('sales_journal')?->journal,
                'accounts' => [
                    ['label' => 'Tresorerie debit', 'value' => $settings->get('sale_cash_account')?->account],
                    ['label' => 'Produit des ventes credit', 'value' => $settings->get('sale_revenue_account')?->account],
                    ['label' => 'Cout des ventes debit', 'value' => $settings->get('sale_cogs_account')?->account],
                    ['label' => 'Stock credit', 'value' => $settings->get('inventory_account')?->account],
                ],
            ],
            [
                'title' => 'Vente a credit',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'La vente a credit constate une creance client jusqu au reglement effectif.',
                'journal' => $settings->get('sales_journal')?->journal,
                'accounts' => [
                    ['label' => 'Client debit', 'value' => $settings->get('sale_credit_account')?->account],
                    ['label' => 'Produit des ventes credit', 'value' => $settings->get('sale_revenue_account')?->account],
                    ['label' => 'Cout des ventes debit', 'value' => $settings->get('sale_cogs_account')?->account],
                    ['label' => 'Stock credit', 'value' => $settings->get('inventory_account')?->account],
                ],
            ],
            [
                'title' => 'Reglement client',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'Le reglement diminue la creance client et alimente la caisse ou la banque selon le mode de paiement.',
                'journal' => $settings->get('sales_journal')?->journal,
                'accounts' => [
                    ['label' => 'Tresorerie debit', 'value' => $settings->get('sale_cash_account')?->account],
                    ['label' => 'Banque debit', 'value' => $settings->get('bank_account')?->account],
                    ['label' => 'Client credit', 'value' => $settings->get('sale_credit_account')?->account],
                ],
            ],
            [
                'title' => 'Achat receptionne',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'Un achat receptionne cree une entree en stock et une dette fournisseur tant qu il n est pas regle.',
                'journal' => $settings->get('purchases_journal')?->journal,
                'accounts' => [
                    ['label' => 'Stock debit', 'value' => $settings->get('inventory_account')?->account],
                    ['label' => 'Fournisseur credit', 'value' => $settings->get('supplier_account')?->account],
                ],
            ],
            [
                'title' => 'Depense',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'La depense enregistre une charge et un decaissement. Le compte de charge depend de la categorie choisie.',
                'journal' => $settings->get('expenses_journal')?->journal,
                'accounts' => [
                    ['label' => 'Charge par defaut debit', 'value' => $settings->get('expense_default_account')?->account],
                    ['label' => 'Transport debit', 'value' => $settings->get('expense_transport_account')?->account],
                    ['label' => 'Loyer/location debit', 'value' => $settings->get('expense_rent_account')?->account],
                    ['label' => 'Services debit', 'value' => $settings->get('expense_utilities_account')?->account],
                    ['label' => 'Tresorerie credit', 'value' => $settings->get('sale_cash_account')?->account],
                    ['label' => 'Banque credit', 'value' => $settings->get('bank_account')?->account],
                ],
            ],
            [
                'title' => 'Ecart inventaire',
                'status' => 'Comptabilisee automatiquement',
                'reason' => 'Le surplus credite un gain d inventaire; le manque debite une perte d inventaire. Les deux ajustent le stock.',
                'journal' => $settings->get('inventory_journal')?->journal,
                'accounts' => [
                    ['label' => 'Stock', 'value' => $settings->get('inventory_account')?->account],
                    ['label' => 'Gain inventaire', 'value' => $settings->get('inventory_gain_account')?->account],
                    ['label' => 'Perte inventaire', 'value' => $settings->get('inventory_loss_account')?->account],
                ],
            ],
            [
                'title' => 'Transfert de stock interne',
                'status' => 'Non comptabilisee par defaut',
                'reason' => 'Le transfert entre deux emplacements internes ne change pas la valeur globale du stock. Il reste trace logistiquement mais sans ecriture comptable par defaut.',
                'journal' => null,
                'accounts' => [],
            ],
            [
                'title' => 'Import produit',
                'status' => 'Non comptabilisee par defaut',
                'reason' => 'L import catalogue sert a creer ou mettre a jour les fiches produit. Il n est pas traite comme une piece comptable autonome.',
                'journal' => null,
                'accounts' => [],
            ],
        ];

        return view('livewire.accounting.operations-map', compact('operations'))
            ->layout('layouts.app');
    }
}
