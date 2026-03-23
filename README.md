# Smart Hardware ERP

ERP léger pour gestion de stock, achats, ventes et mouvements, construit avec Laravel + Livewire et Tailwind CSS.

**Fonctionnalités**
- Authentification sécurisée avec vérification d’e-mail
- Gestion des utilisateurs avec rôles `owner`, `manager`, `seller`
- Paramètres entreprise (logo, infos légales, pied de facture)
- Produits, fournisseurs, clients
- Stock dynamique par magasin/dépôt
- Mouvements de stock (entrées, sorties, transferts)
- Achats, ventes, inventaires, dépenses
- Rapports de stock et finances
- Unités dynamiques (ex: `pcs`, `kg`, `g`, `l`, `m`)

**Technologies**
- Laravel + Livewire
- Tailwind CSS + Vite
- SQLite par défaut (compatible MySQL/PostgreSQL)

## Prérequis
- PHP 8.2+
- Composer
- Node.js 18+

## Installation rapide
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Démarrage local
```bash
composer dev
```

## Comptes de démonstration
- Admin: `admin@local.test`
- Mot de passe: `Admin@12345`

Rôles disponibles dans l’application: `owner`, `manager`, `seller`.

## Données de seed
Les seeders créent:
- 1 admin + utilisateurs de démonstration
- Unités de mesure standard
- Magasins/dépôts de base
- Paramètres entreprise par défaut
- Données de démo (produits, clients, fournisseurs)

## Import produits (CSV / Excel)
Colonnes supportées:
- `name`
- `sku`
- `barcode`
- `unit_code` (ex: `pcs`, `kg`)
- `description`
- `margin`
- `reorder_level`

Si `unit_code` est absent, l’unité par défaut est `pcs`.

## Sécurité
- Les rôles protègent les routes sensibles via middleware
- L’e-mail doit être vérifié avant l’accès au tableau de bord
- Les mots de passe sont stockés via hash sécurisé

## Notes
- Les pages passent par Livewire, pas de contrôleurs classiques.
- Les assets Vite sont compilés via `npm run build`.
