# Guide Utilisateur

## 1. Présentation

Smart Hardware ERP est une application de gestion pour:

- les produits
- le stock par dépôt ou magasin
- les achats
- les ventes
- les transferts de stock
- les inventaires
- les dépenses
- les rapports

L'application fonctionne avec des rôles utilisateurs et un accès contrôlé selon les responsabilités.

## 2. Connexion

Accédez à l'application avec votre compte utilisateur.

Compte de démonstration principal:

- Email: `admin@local.test`
- Mot de passe: `Admin@12345`

Important:

- l'inscription publique est désactivée
- les nouveaux utilisateurs doivent être créés depuis le module `Utilisateurs`
- certains écrans demandent un code secret pour confirmer une suppression

## 3. Rôles utilisateurs

Les rôles disponibles sont:

- `owner`: accès complet
- `manager`: gestion opérationnelle avancée
- `seller`: accès orienté ventes et opérations autorisées

Selon le rôle, certaines pages ou actions peuvent être masquées ou bloquées.

## 4. Navigation générale

Après connexion, vous pouvez accéder aux principaux modules:

- Tableau de bord
- Produits
- Fournisseurs
- Clients
- Achats
- Ventes
- Mouvements de stock
- Transferts
- Inventaires
- Dépenses
- Rapports
- Utilisateurs
- Paramètres société
- Sauvegardes
- Corbeille

Le tableau de bord affiche les indicateurs clés, les alertes de stock et les dernières activités.

## 5. Gestion des produits

Dans le module `Produits`, vous pouvez:

- créer un article
- modifier un article
- consulter la quantité disponible
- consulter la fiche stock
- importer des produits par fichier CSV ou Excel

Informations principales d'un produit:

- nom
- SKU
- code-barres
- unité
- coût moyen
- prix de vente
- marge
- seuil de réapprovisionnement

### Import produits

Colonnes supportées:

- `sku`
- `name`
- `barcode`
- `unit_code`
- `description`
- `cost`
- `price`
- `stock`
- `margin`
- `reorder_level`

Si `stock` est renseigné, la quantité est ajoutée dans l'entité de stock choisie pendant l'import.

## 6. Fiche stock produit

La fiche stock permet de voir:

- le stock total
- le seuil de réapprovisionnement
- la valeur du stock
- le stock par emplacement
- l'historique des mouvements

Les quantités sont affichées de manière simple. Exemple:

- `3` au lieu de `3.000`
- `2.5` si la quantité contient une décimale utile

## 7. Fournisseurs et clients

Les modules `Fournisseurs` et `Clients` servent à:

- enregistrer les partenaires
- modifier leurs informations
- centraliser les données utilisées dans les achats et ventes

Avant de saisir des achats ou ventes réels, il est conseillé de préparer vos fournisseurs et clients.

## 8. Achats

Le module `Achats` permet:

- de créer une commande d'achat
- de modifier une commande
- de visualiser les lignes commandées
- de suivre les quantités reçues
- d'imprimer le bon de commande

Pendant la réception, le stock peut être injecté dans un emplacement choisi.

Bonnes pratiques:

- vérifiez le fournisseur avant validation
- contrôlez les quantités reçues
- confirmez le dépôt ou magasin de réception

## 9. Ventes

Le module `Ventes` permet:

- d'ajouter des articles à vendre
- de choisir le magasin ou dépôt de sortie
- de vérifier le stock disponible
- d'enregistrer la vente
- d'imprimer le document associé
- de traiter des ajustements après vente selon les options disponibles

Avant validation:

- vérifiez la disponibilité réelle du stock
- contrôlez les quantités saisies
- confirmez les montants et remises

## 10. Mouvements de stock

Le journal des mouvements centralise les opérations de stock:

- entrées
- sorties
- ajustements
- transferts

Ce module est utile pour:

- retracer une variation
- contrôler une anomalie
- auditer l'historique d'un article

## 11. Transferts de stock

Le module `Transferts` permet de déplacer des articles d'un emplacement vers un autre.

Étapes générales:

1. choisir l'emplacement source
2. choisir l'emplacement de destination
3. sélectionner les articles
4. saisir les quantités
5. confirmer le transfert

L'application vérifie le stock disponible avant validation.

## 12. Inventaires

Le module `Inventaires` sert à comparer le stock système et le stock compté.

Vous pouvez:

- créer un inventaire manuel
- importer un inventaire par fichier
- analyser les écarts
- régulariser les différences

Ce module aide à détecter:

- les manquants
- les surplus
- les erreurs de saisie ou de mouvement

## 13. Dépenses

Le module `Dépenses` permet d'enregistrer les charges de l'entreprise.

Exemples:

- transport
- loyer
- maintenance
- fournitures

Ces données alimentent le suivi financier et les rapports.

## 14. Rapports

Deux grands types de rapports sont disponibles:

- rapport financier
- rapport des ventes

Ils permettent de suivre notamment:

- les quantités vendues
- les montants de ventes
- la valeur du stock
- les marges et coûts

## 15. Utilisateurs et sécurité

Le module `Utilisateurs` permet aux profils autorisés de:

- créer un utilisateur
- attribuer un rôle
- affecter un emplacement de stock si nécessaire
- modifier un compte existant

Rappels de sécurité:

- ne partagez pas les mots de passe
- changez les accès des comptes inutilisés
- limitez les droits selon le rôle réel de l'utilisateur
- utilisez le code secret de suppression avec précaution

## 16. Paramètres société

Le module `Paramètres société` permet de configurer:

- les informations de l'entreprise
- certains réglages de présentation
- les seuils globaux de stock bas
- les éléments visibles sur les documents

Il est conseillé de compléter ces informations avant l'utilisation quotidienne.

## 17. Sauvegardes et santé système

Le module `Sauvegardes` permet de récupérer des instantanés de données selon la configuration en place.

Le module `Santé système` aide à contrôler:

- l'état général de l'application
- certains services ou dépendances
- le stockage et la configuration

## 18. Corbeille

La corbeille permet de retrouver certains éléments supprimés en soft delete.

Elle sert à:

- restaurer un enregistrement supprimé
- contrôler les suppressions récentes
- éviter des pertes accidentelles

## 19. Bonnes pratiques d'utilisation

- créez d'abord les unités, emplacements, fournisseurs et clients
- ajoutez les produits avant les opérations de stock
- utilisez les achats pour alimenter le stock réel
- faites les ventes uniquement depuis le bon emplacement
- contrôlez régulièrement les alertes de stock bas
- réalisez des inventaires périodiques
- consultez les rapports pour suivre la performance

## 20. Résolution rapide des problèmes

### Impossible d'accéder à une page

Causes possibles:

- utilisateur non connecté
- e-mail non vérifié
- rôle insuffisant
- emplacement non autorisé

### Quantité insuffisante

Vérifiez:

- l'emplacement sélectionné
- la quantité réellement disponible
- les transferts ou ventes déjà enregistrés

### Utilisateur introuvable pour l'inscription

L'inscription publique est fermée. Il faut demander à un `owner` ou `manager` autorisé de créer le compte depuis le module `Utilisateurs`.

## 21. Conclusion

Pour bien démarrer:

1. connectez-vous
2. configurez l'entreprise
3. créez les emplacements et unités si besoin
4. ajoutez les produits
5. enregistrez les achats
6. effectuez les ventes et suivis de stock
7. contrôlez les rapports et inventaires régulièrement
