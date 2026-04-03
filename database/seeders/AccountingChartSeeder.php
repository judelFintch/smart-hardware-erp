<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Database\Seeder;

class AccountingChartSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->accounts() as $account) {
            Account::updateOrCreate(
                ['number' => $account['number']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'category' => $account['category'],
                    'is_system' => true,
                ]
            );
        }

        app(AccountingService::class)->ensureDefaults();
    }

    private function accounts(): array
    {
        return [
            ['number' => '10', 'name' => 'Capital', 'type' => 'equity', 'category' => 'capital'],
            ['number' => '101', 'name' => 'Capital social', 'type' => 'equity', 'category' => 'capital'],
            ['number' => '104', 'name' => 'Primes liees au capital', 'type' => 'equity', 'category' => 'capital'],
            ['number' => '106', 'name' => 'Reserves', 'type' => 'equity', 'category' => 'reserves'],
            ['number' => '109', 'name' => 'Actionnaires capital souscrit non appele', 'type' => 'asset', 'category' => 'capital_receivable'],

            ['number' => '12', 'name' => 'Report a nouveau', 'type' => 'equity', 'category' => 'retained_earnings'],
            ['number' => '13', 'name' => 'Resultat net de l exercice', 'type' => 'equity', 'category' => 'net_income'],
            ['number' => '14', 'name' => 'Subventions d investissement', 'type' => 'liability', 'category' => 'grants'],
            ['number' => '15', 'name' => 'Provisions reglementees et fonds assimiles', 'type' => 'liability', 'category' => 'regulated_provisions'],
            ['number' => '16', 'name' => 'Emprunts et dettes assimilees', 'type' => 'liability', 'category' => 'borrowings'],
            ['number' => '165', 'name' => 'Depots et cautionnements recus', 'type' => 'liability', 'category' => 'guarantee_deposits_received'],
            ['number' => '166', 'name' => 'Interets courus', 'type' => 'liability', 'category' => 'accrued_interest'],
            ['number' => '17', 'name' => 'Dettes de credit-bail et contrats assimiles', 'type' => 'liability', 'category' => 'lease_liabilities'],
            ['number' => '18', 'name' => 'Dettes liees a des participations et comptes de liaison', 'type' => 'liability', 'category' => 'related_party_liabilities'],
            ['number' => '19', 'name' => 'Provisions financieres pour risques et charges', 'type' => 'liability', 'category' => 'provisions'],

            ['number' => '20', 'name' => 'Charges immobilisees', 'type' => 'asset', 'category' => 'capitalized_costs'],
            ['number' => '21', 'name' => 'Immobilisations incorporelles', 'type' => 'asset', 'category' => 'intangible_assets'],
            ['number' => '22', 'name' => 'Terrains', 'type' => 'asset', 'category' => 'land'],
            ['number' => '23', 'name' => 'Batiments installations techniques et agencements', 'type' => 'asset', 'category' => 'buildings'],
            ['number' => '24', 'name' => 'Materiel', 'type' => 'asset', 'category' => 'equipment'],
            ['number' => '244', 'name' => 'Materiel et mobilier de bureau', 'type' => 'asset', 'category' => 'office_equipment'],
            ['number' => '245', 'name' => 'Materiel de transport', 'type' => 'asset', 'category' => 'vehicles'],
            ['number' => '246', 'name' => 'Avances et acomptes verses sur immobilisations', 'type' => 'asset', 'category' => 'fixed_asset_advances'],
            ['number' => '247', 'name' => 'Immobilisations en cours', 'type' => 'asset', 'category' => 'assets_in_progress'],
            ['number' => '248', 'name' => 'Autres immobilisations corporelles', 'type' => 'asset', 'category' => 'other_fixed_assets'],
            ['number' => '26', 'name' => 'Titres de participation', 'type' => 'asset', 'category' => 'investments'],
            ['number' => '27', 'name' => 'Autres immobilisations financieres', 'type' => 'asset', 'category' => 'financial_assets'],
            ['number' => '28', 'name' => 'Amortissements', 'type' => 'asset', 'category' => 'depreciation'],
            ['number' => '29', 'name' => 'Provisions pour depreciation des immobilisations', 'type' => 'asset', 'category' => 'asset_impairment'],

            ['number' => '31', 'name' => 'Stocks de marchandises', 'type' => 'asset', 'category' => 'inventory_merchandise'],
            ['number' => '32', 'name' => 'Stocks de matieres premieres et fournitures liees', 'type' => 'asset', 'category' => 'inventory_raw_materials'],
            ['number' => '33', 'name' => 'Autres approvisionnements', 'type' => 'asset', 'category' => 'inventory_supplies'],
            ['number' => '34', 'name' => 'Stocks de produits en cours', 'type' => 'asset', 'category' => 'inventory_work_in_progress'],
            ['number' => '35', 'name' => 'Stocks de services en cours', 'type' => 'asset', 'category' => 'inventory_services_in_progress'],
            ['number' => '36', 'name' => 'Stocks de produits finis', 'type' => 'asset', 'category' => 'inventory_finished_goods'],
            ['number' => '37', 'name' => 'Stocks de produits intermediaires et residuels', 'type' => 'asset', 'category' => 'inventory_intermediate_goods'],
            ['number' => '38', 'name' => 'Stocks en cours de route en consignation ou en depot', 'type' => 'asset', 'category' => 'inventory_in_transit'],
            ['number' => '39', 'name' => 'Provisions pour depreciation des stocks', 'type' => 'asset', 'category' => 'inventory_impairment'],

            ['number' => '40', 'name' => 'Fournisseurs et comptes rattaches', 'type' => 'liability', 'category' => 'suppliers'],
            ['number' => '401', 'name' => 'Fournisseurs', 'type' => 'liability', 'category' => 'supplier_trade_payable'],
            ['number' => '408', 'name' => 'Fournisseurs factures non parvenues', 'type' => 'liability', 'category' => 'supplier_accruals'],
            ['number' => '409', 'name' => 'Fournisseurs debiteurs avances et acomptes verses', 'type' => 'asset', 'category' => 'supplier_advances'],
            ['number' => '41', 'name' => 'Clients et comptes rattaches', 'type' => 'asset', 'category' => 'customers'],
            ['number' => '411', 'name' => 'Clients', 'type' => 'asset', 'category' => 'customer_trade_receivable'],
            ['number' => '416', 'name' => 'Clients douteux ou litigieux', 'type' => 'asset', 'category' => 'doubtful_customers'],
            ['number' => '418', 'name' => 'Clients produits non encore factures', 'type' => 'asset', 'category' => 'customer_accruals'],
            ['number' => '419', 'name' => 'Clients crediteurs avances recus', 'type' => 'liability', 'category' => 'customer_advances'],
            ['number' => '42', 'name' => 'Personnel', 'type' => 'liability', 'category' => 'payroll'],
            ['number' => '43', 'name' => 'Organismes sociaux', 'type' => 'liability', 'category' => 'social_security'],
            ['number' => '44', 'name' => 'Etat et collectivites publiques', 'type' => 'liability', 'category' => 'taxes'],
            ['number' => '445', 'name' => 'Etat TVA recupérable', 'type' => 'asset', 'category' => 'vat_recoverable'],
            ['number' => '447', 'name' => 'Etat TVA facturée', 'type' => 'liability', 'category' => 'vat_collected'],
            ['number' => '448', 'name' => 'Etat charges a payer et produits a recevoir', 'type' => 'liability', 'category' => 'state_accruals'],
            ['number' => '45', 'name' => 'Organismes internationaux', 'type' => 'liability', 'category' => 'international_agencies'],
            ['number' => '46', 'name' => 'Associes et groupe', 'type' => 'asset', 'category' => 'related_parties'],
            ['number' => '47', 'name' => 'Debiteurs et crediteurs divers', 'type' => 'asset', 'category' => 'other_third_parties'],
            ['number' => '48', 'name' => 'Creances et dettes hors activites ordinaires', 'type' => 'asset', 'category' => 'non_operating_balances'],
            ['number' => '49', 'name' => 'Provisions pour depreciation et risques a court terme', 'type' => 'asset', 'category' => 'current_impairment'],

            ['number' => '50', 'name' => 'Titres de placement', 'type' => 'asset', 'category' => 'marketable_securities'],
            ['number' => '51', 'name' => 'Valeurs a encaisser', 'type' => 'asset', 'category' => 'cash_items'],
            ['number' => '52', 'name' => 'Banques', 'type' => 'asset', 'category' => 'bank'],
            ['number' => '521', 'name' => 'Banques locales', 'type' => 'asset', 'category' => 'bank_local'],
            ['number' => '522', 'name' => 'Banques etrangeres', 'type' => 'asset', 'category' => 'bank_foreign'],
            ['number' => '53', 'name' => 'Etablissements financiers et assimilés', 'type' => 'asset', 'category' => 'financial_institutions'],
            ['number' => '54', 'name' => 'Instruments de tresorerie', 'type' => 'asset', 'category' => 'treasury_instruments'],
            ['number' => '55', 'name' => 'Instruments financiers a terme et options de tresorerie', 'type' => 'asset', 'category' => 'treasury_derivatives'],
            ['number' => '56', 'name' => 'Banques credits de tresorerie et d escompte', 'type' => 'liability', 'category' => 'bank_short_term_credit'],
            ['number' => '57', 'name' => 'Caisse', 'type' => 'asset', 'category' => 'cash'],
            ['number' => '571', 'name' => 'Caisse', 'type' => 'asset', 'category' => 'cash_main'],
            ['number' => '58', 'name' => 'Regies d avances accreditifs et virements internes', 'type' => 'asset', 'category' => 'internal_transfers'],
            ['number' => '59', 'name' => 'Provisions pour depreciation des comptes financiers', 'type' => 'asset', 'category' => 'financial_impairment'],

            ['number' => '60', 'name' => 'Achats et variations de stocks', 'type' => 'expense', 'category' => 'purchases'],
            ['number' => '601', 'name' => 'Achats de marchandises', 'type' => 'expense', 'category' => 'purchases_merchandise'],
            ['number' => '602', 'name' => 'Achats de matieres premieres et fournitures liees', 'type' => 'expense', 'category' => 'purchases_raw_materials'],
            ['number' => '603', 'name' => 'Variations des stocks de biens achetes', 'type' => 'expense', 'category' => 'inventory_variation'],
            ['number' => '604', 'name' => 'Achats stockes de matieres et fournitures consommables', 'type' => 'expense', 'category' => 'consumable_purchases'],
            ['number' => '605', 'name' => 'Autres achats', 'type' => 'expense', 'category' => 'other_purchases'],
            ['number' => '608', 'name' => 'Achats d emballages', 'type' => 'expense', 'category' => 'packaging_purchases'],
            ['number' => '61', 'name' => 'Transports', 'type' => 'expense', 'category' => 'transport'],
            ['number' => '611', 'name' => 'Transport sur achats', 'type' => 'expense', 'category' => 'transport_purchases'],
            ['number' => '612', 'name' => 'Transport sur ventes', 'type' => 'expense', 'category' => 'transport_sales'],
            ['number' => '613', 'name' => 'Transport pour le compte de tiers', 'type' => 'expense', 'category' => 'transport_third_party'],
            ['number' => '614', 'name' => 'Transport du personnel', 'type' => 'expense', 'category' => 'transport_staff'],
            ['number' => '62', 'name' => 'Services exterieurs A', 'type' => 'expense', 'category' => 'external_services_a'],
            ['number' => '621', 'name' => 'Sous-traitance generale', 'type' => 'expense', 'category' => 'subcontracting'],
            ['number' => '622', 'name' => 'Locations et charges locatives', 'type' => 'expense', 'category' => 'rent'],
            ['number' => '623', 'name' => 'Redevances de credit-bail locations acquisition', 'type' => 'expense', 'category' => 'leasing'],
            ['number' => '624', 'name' => 'Entretien reparations et maintenance', 'type' => 'expense', 'category' => 'maintenance'],
            ['number' => '625', 'name' => 'Primes d assurances', 'type' => 'expense', 'category' => 'insurance'],
            ['number' => '626', 'name' => 'Etudes recherches et documentation', 'type' => 'expense', 'category' => 'research'],
            ['number' => '627', 'name' => 'Publicite publications relations publiques', 'type' => 'expense', 'category' => 'marketing'],
            ['number' => '628', 'name' => 'Frais de telecommunications', 'type' => 'expense', 'category' => 'telecom'],
            ['number' => '63', 'name' => 'Services exterieurs B', 'type' => 'expense', 'category' => 'external_services_b'],
            ['number' => '631', 'name' => 'Frais bancaires', 'type' => 'expense', 'category' => 'bank_fees'],
            ['number' => '632', 'name' => 'Remunerations d intermediaires et honoraires', 'type' => 'expense', 'category' => 'professional_fees'],
            ['number' => '633', 'name' => 'Frais de formation du personnel', 'type' => 'expense', 'category' => 'training'],
            ['number' => '634', 'name' => 'Redevances pour brevets licences logiciels', 'type' => 'expense', 'category' => 'licenses'],
            ['number' => '635', 'name' => 'Cotisations', 'type' => 'expense', 'category' => 'subscriptions'],
            ['number' => '636', 'name' => 'Frais de mission', 'type' => 'expense', 'category' => 'travel'],
            ['number' => '637', 'name' => 'Frais postaux', 'type' => 'expense', 'category' => 'postage'],
            ['number' => '638', 'name' => 'Autres frais et commissions', 'type' => 'expense', 'category' => 'other_service_fees'],
            ['number' => '64', 'name' => 'Impots et taxes', 'type' => 'expense', 'category' => 'tax_expense'],
            ['number' => '65', 'name' => 'Autres charges', 'type' => 'expense', 'category' => 'other_operating_expense'],
            ['number' => '66', 'name' => 'Charges de personnel', 'type' => 'expense', 'category' => 'payroll_expense'],
            ['number' => '67', 'name' => 'Frais financiers et charges assimilees', 'type' => 'expense', 'category' => 'financial_expense'],
            ['number' => '68', 'name' => 'Dotations aux amortissements et provisions', 'type' => 'expense', 'category' => 'depreciation_expense'],
            ['number' => '69', 'name' => 'Impots sur les resultats et assimilés', 'type' => 'expense', 'category' => 'income_tax'],

            ['number' => '70', 'name' => 'Ventes', 'type' => 'revenue', 'category' => 'sales'],
            ['number' => '701', 'name' => 'Ventes de marchandises', 'type' => 'revenue', 'category' => 'sales_merchandise'],
            ['number' => '702', 'name' => 'Ventes de produits finis', 'type' => 'revenue', 'category' => 'sales_finished_goods'],
            ['number' => '703', 'name' => 'Ventes de produits intermediaires et residuels', 'type' => 'revenue', 'category' => 'sales_intermediate_goods'],
            ['number' => '704', 'name' => 'Travaux', 'type' => 'revenue', 'category' => 'revenue_works'],
            ['number' => '705', 'name' => 'Services vendus', 'type' => 'revenue', 'category' => 'revenue_services'],
            ['number' => '706', 'name' => 'Produits accessoires', 'type' => 'revenue', 'category' => 'ancillary_revenue'],
            ['number' => '707', 'name' => 'Ports et frais accessoires factures', 'type' => 'revenue', 'category' => 'rebilled_charges'],
            ['number' => '708', 'name' => 'Autres produits', 'type' => 'revenue', 'category' => 'other_revenue'],
            ['number' => '709', 'name' => 'Rabais remises et ristournes accordes', 'type' => 'revenue', 'category' => 'sales_discounts'],
            ['number' => '71', 'name' => 'Subventions d exploitation', 'type' => 'revenue', 'category' => 'operating_grants'],
            ['number' => '72', 'name' => 'Production immobilisee', 'type' => 'revenue', 'category' => 'capitalized_production'],
            ['number' => '73', 'name' => 'Variation des stocks de biens et services produits', 'type' => 'revenue', 'category' => 'production_variation'],
            ['number' => '74', 'name' => 'Production stockee', 'type' => 'revenue', 'category' => 'stored_production'],
            ['number' => '75', 'name' => 'Autres produits', 'type' => 'revenue', 'category' => 'other_operating_income'],
            ['number' => '76', 'name' => 'Produits financiers et assimilés', 'type' => 'revenue', 'category' => 'financial_income'],
            ['number' => '77', 'name' => 'Revenus des titres de placement', 'type' => 'revenue', 'category' => 'investment_income'],
            ['number' => '78', 'name' => 'Transferts de charges', 'type' => 'revenue', 'category' => 'expense_transfers'],
            ['number' => '79', 'name' => 'Reprises de provisions et amortissements', 'type' => 'revenue', 'category' => 'reversals'],

            ['number' => '80', 'name' => 'Charges et produits hors activites ordinaires', 'type' => 'equity', 'category' => 'non_operating'],
            ['number' => '81', 'name' => 'Valeurs comptables des cessions d immobilisations', 'type' => 'expense', 'category' => 'disposal_loss'],
            ['number' => '82', 'name' => 'Produits des cessions d immobilisations', 'type' => 'revenue', 'category' => 'disposal_gain'],
            ['number' => '83', 'name' => 'Charges hors activites ordinaires', 'type' => 'expense', 'category' => 'exceptional_expense'],
            ['number' => '84', 'name' => 'Produits hors activites ordinaires', 'type' => 'revenue', 'category' => 'exceptional_income'],
            ['number' => '85', 'name' => 'Dotations hors activites ordinaires', 'type' => 'expense', 'category' => 'exceptional_allocation'],
            ['number' => '86', 'name' => 'Reprises hors activites ordinaires', 'type' => 'revenue', 'category' => 'exceptional_reversal'],
            ['number' => '87', 'name' => 'Participation des travailleurs', 'type' => 'expense', 'category' => 'profit_sharing'],
            ['number' => '88', 'name' => 'Subventions d equilibre', 'type' => 'revenue', 'category' => 'balance_grants'],
            ['number' => '89', 'name' => 'Impots sur le resultat hors activites ordinaires', 'type' => 'expense', 'category' => 'exceptional_tax'],
        ];
    }
}
