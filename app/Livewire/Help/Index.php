<?php

namespace App\Livewire\Help;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public function render()
    {
        $sections = $this->guideSections()->map(function (array $section) {
            $route = $this->sectionRoute($section['title']);

            return [
                'title' => $section['title'],
                'anchor' => $section['anchor'],
                'summary' => $this->extractSummary($section['content']),
                'html' => Str::markdown($section['content']),
                'route' => $this->canAccessRoute($route) ? $route : null,
            ];
        })->filter(function (array $section) {
            $query = trim(Str::lower($this->search));

            if ($query === '') {
                return true;
            }

            $haystack = Str::lower($section['title'] . ' ' . strip_tags($section['html']));

            return Str::contains($haystack, $query);
        })->values();

        $quickActions = collect([
            ['title' => 'Créer un produit', 'description' => 'Ajouter un nouvel article avec prix, unité et seuil.', 'route' => 'products.create'],
            ['title' => 'Importer des produits', 'description' => 'Charger rapidement un fichier CSV ou Excel.', 'route' => 'products.index'],
            ['title' => 'Enregistrer un achat', 'description' => 'Ajouter du stock depuis un fournisseur.', 'route' => 'purchases.create'],
            ['title' => 'Enregistrer une vente', 'description' => 'Sortir du stock depuis un magasin.', 'route' => 'sales.create'],
            ['title' => 'Faire un transfert', 'description' => 'Déplacer des articles entre deux emplacements.', 'route' => 'stock-transfers.create'],
            ['title' => 'Lancer un inventaire', 'description' => 'Comparer le stock réel avec le stock système.', 'route' => 'inventory-counts.create'],
        ])->filter(fn (array $item) => $this->canAccessRoute($item['route']))->values();

        $smartGuides = collect([
            ['title' => 'Je veux commencer correctement', 'description' => 'Voir dans quel ordre configurer le système avant utilisation.', 'anchor' => '21-conclusion'],
            ['title' => 'Je veux comprendre les produits et le stock', 'description' => 'Trouver où créer les articles et lire la fiche stock.', 'anchor' => '5-gestion-des-produits'],
            ['title' => 'Je veux faire un achat', 'description' => 'Comprendre comment faire entrer du stock dans le système.', 'anchor' => '8-achats'],
            ['title' => 'Je veux faire une vente', 'description' => 'Vendre avec le bon emplacement et la bonne quantité.', 'anchor' => '9-ventes'],
            ['title' => 'Je veux corriger un écart de stock', 'description' => 'Passer par l’inventaire et vérifier les mouvements.', 'anchor' => '12-inventaires'],
            ['title' => 'Je veux savoir pourquoi un accès est bloqué', 'description' => 'Comprendre les rôles, restrictions et erreurs fréquentes.', 'anchor' => '20-resolution-rapide-des-problemes'],
        ])->filter(function (array $item) use ($sections) {
            return $sections->contains(fn (array $section) => $section['anchor'] === $item['anchor']);
        })->values();

        $onboardingSteps = collect([
            [
                'title' => 'Configurer l’entreprise',
                'description' => 'Complétez les informations de base et les seuils globaux.',
                'done' => CompanySetting::query()->whereNotNull('name')->exists(),
                'route' => 'company.settings',
            ],
            [
                'title' => 'Créer les unités',
                'description' => 'Préparez les unités comme pcs, kg, litre ou mètre.',
                'done' => Unit::query()->count() > 0,
                'route' => 'units.index',
            ],
            [
                'title' => 'Créer les emplacements',
                'description' => 'Ajoutez les dépôts et magasins utilisés dans le stock.',
                'done' => StockLocation::query()->count() > 0,
                'route' => 'stock-locations.index',
            ],
            [
                'title' => 'Créer les produits',
                'description' => 'Ajoutez les articles ou importez-les par fichier.',
                'done' => Product::query()->count() > 0,
                'route' => 'products.index',
            ],
            [
                'title' => 'Créer les fournisseurs et clients',
                'description' => 'Préparez les partenaires avant les opérations réelles.',
                'done' => Supplier::query()->count() > 0 && Customer::query()->count() > 0,
                'route' => 'suppliers.index',
            ],
            [
                'title' => 'Créer les utilisateurs',
                'description' => 'Ajoutez les comptes nécessaires selon les rôles.',
                'done' => User::query()->count() > 1,
                'route' => 'users.index',
            ],
        ])->filter(fn (array $step) => $this->canAccessRoute($step['route']))->values();

        $faqItems = collect([
            [
                'question' => 'Pourquoi je ne peux pas accéder à une page ?',
                'answer' => 'En général, cela vient du rôle utilisateur, de l’e-mail non vérifié, ou d’un emplacement non autorisé. Vérifiez aussi que vous êtes connecté avec le bon compte.',
                'route' => 'help.index',
                'anchor' => '20-resolution-rapide-des-problemes',
            ],
            [
                'question' => 'Pourquoi une vente est refusée ?',
                'answer' => 'La cause la plus fréquente est un stock insuffisant dans le magasin choisi. Vérifiez la quantité disponible, l’emplacement sélectionné et les lignes déjà saisies.',
                'route' => 'sales.create',
                'anchor' => null,
            ],
            [
                'question' => 'Pourquoi mon stock semble faux ?',
                'answer' => 'Commencez par consulter la fiche stock du produit, puis le journal des mouvements, les achats, les ventes, les transferts et les inventaires récents.',
                'route' => 'stock-movements.index',
                'anchor' => null,
            ],
            [
                'question' => 'Comment ajouter rapidement beaucoup de produits ?',
                'answer' => 'Utilisez l’import CSV ou Excel depuis le module Produits. Vérifiez d’abord les colonnes, l’unité et l’emplacement de stock choisi.',
                'route' => 'products.index',
                'anchor' => null,
            ],
            [
                'question' => 'À quoi sert l’inventaire ?',
                'answer' => 'L’inventaire sert à comparer le stock système avec le stock réellement compté afin de corriger les écarts et fiabiliser les quantités.',
                'route' => 'inventory-counts.index',
                'anchor' => null,
            ],
        ])->filter(function (array $item) {
            return $this->canAccessRoute($item['route']) || $item['route'] === 'help.index';
        })->values();

        return view('livewire.help.index', compact('sections', 'quickActions', 'smartGuides', 'onboardingSteps', 'faqItems'))
            ->layout('layouts.app');
    }

    private function guideSections(): Collection
    {
        $content = file_get_contents(base_path('docs/GUIDE_UTILISATEUR.md')) ?: '';
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];

        $sections = [];
        $currentTitle = null;
        $currentLines = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, '## ')) {
                if ($currentTitle !== null) {
                    $sections[] = $this->makeSection($currentTitle, $currentLines);
                }

                $currentTitle = trim(Str::after($line, '## '));
                $currentLines = [];

                continue;
            }

            if ($currentTitle !== null) {
                $currentLines[] = $line;
            }
        }

        if ($currentTitle !== null) {
            $sections[] = $this->makeSection($currentTitle, $currentLines);
        }

        return collect($sections);
    }

    private function makeSection(string $title, array $lines): array
    {
        return [
            'title' => $title,
            'anchor' => Str::slug($title),
            'content' => trim(implode("\n", $lines)),
        ];
    }

    private function extractSummary(string $content): string
    {
        $paragraphs = preg_split("/\n\s*\n/", trim($content)) ?: [];
        $firstParagraph = trim((string) ($paragraphs[0] ?? ''));

        return Str::of($firstParagraph)
            ->replaceMatches('/^\s*[-*]\s*/m', '')
            ->replace('`', '')
            ->squish()
            ->limit(140)
            ->toString();
    }

    private function sectionRoute(string $title): ?string
    {
        return match ($title) {
            '4. Navigation générale' => 'dashboard',
            '5. Gestion des produits', '6. Fiche stock produit' => 'products.index',
            '7. Fournisseurs et clients' => 'suppliers.index',
            '8. Achats' => 'purchases.index',
            '9. Ventes' => 'sales.index',
            '10. Mouvements de stock' => 'stock-movements.index',
            '11. Transferts de stock' => 'stock-transfers.create',
            '12. Inventaires' => 'inventory-counts.index',
            '13. Dépenses' => 'expenses.index',
            '14. Rapports' => 'reports.financial',
            '15. Utilisateurs et sécurité' => 'users.index',
            '16. Paramètres société' => 'company.settings',
            '17. Sauvegardes et santé système' => 'system.backups',
            '18. Corbeille' => 'trash.index',
            default => null,
        };
    }

    private function canAccessRoute(?string $route): bool
    {
        if (!$route || !auth()->check()) {
            return false;
        }

        $managerRoutes = [
            'dashboard',
            'products.create',
            'inventory-counts.create',
            'inventory-counts.index',
            'reports.financial',
            'users.index',
            'company.settings',
            'system.backups',
            'trash.index',
        ];

        if (in_array($route, $managerRoutes, true)) {
            return in_array(auth()->user()->role, ['owner', 'manager'], true);
        }

        return true;
    }
}
