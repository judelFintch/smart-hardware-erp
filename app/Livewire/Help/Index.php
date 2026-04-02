<?php

namespace App\Livewire\Help;

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

        return view('livewire.help.index', compact('sections', 'quickActions', 'smartGuides'))
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
