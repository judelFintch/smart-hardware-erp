# Guide Utilisateur

## 1. Présentation

Smart Hardware ERP est une application de gestion conçue pour suivre les opérations quotidiennes d'une quincaillerie ou d'un commerce similaire.

Elle sert principalement à gérer:

- les produits
- le stock par dépôt ou magasin
- les achats
- les ventes
- les transferts de stock
- les inventaires
- les dépenses
- les rapports

Ce qu'il faut retenir:

- le système fonctionne avec des rôles utilisateurs
- certaines actions dépendent du rôle et de l'emplacement autorisé
- chaque opération importante laisse une trace dans le système

## 2. Connexion

### À quoi ça sert

La connexion permet d'accéder au système avec des droits adaptés à votre rôle.

### Compte de démonstration principal

- Email: `admin@local.test`
- Mot de passe: `Admin@12345`

### Important

- l'inscription publique est désactivée
- les nouveaux utilisateurs doivent être créés depuis le module `Utilisateurs`
- certains écrans demandent un code secret pour confirmer une suppression

### Conseil

- utilisez un compte personnel pour chaque utilisateur
- évitez de partager le même mot de passe entre plusieurs personnes

## 3. Rôles utilisateurs

### Les rôles disponibles

- `owner`
  Explication: accès complet à l'application et aux réglages sensibles.
- `manager`
  Explication: gestion opérationnelle avancée, rapports, utilisateurs et configuration autorisée.
- `seller`
  Explication: accès orienté ventes et opérations courantes autorisées.

### À savoir

- un utilisateur peut voir moins d'écrans qu'un autre
- certaines actions sont bloquées si le rôle est insuffisant
- certains comptes sont limités à un emplacement de stock

## 4. Navigation générale

### Modules principaux

- `Tableau de bord`
  Explication: vue générale des indicateurs, alertes et dernières activités.
- `Produits`
  Explication: gestion des articles, quantités et fiche stock.
- `Fournisseurs`
  Explication: partenaires utilisés dans les achats.
- `Clients`
  Explication: partenaires utilisés dans les ventes.
- `Achats`
  Explication: entrée de stock et suivi des commandes d'achat.
- `Ventes`
  Explication: sortie de stock et enregistrement des ventes.
- `Mouvements`
  Explication: journal complet des entrées, sorties et ajustements.
- `Transferts`
  Explication: déplacement de stock entre emplacements.
- `Inventaires`
  Explication: comparaison entre stock système et stock réel.
- `Dépenses`
  Explication: enregistrement des charges de l'entreprise.
- `Rapports`
  Explication: suivi des ventes, coûts, marges et valeurs de stock.
- `Utilisateurs`
  Explication: création et gestion des comptes.
- `Paramètres société`
  Explication: réglages généraux de l'entreprise.
- `Sauvegardes`
  Explication: récupération d'instantanés ou exports selon la configuration.
- `Corbeille`
  Explication: restauration des éléments supprimés en soft delete.

### Conseil

- commencez toujours par identifier le bon module avant de saisir une opération
- si vous hésitez, utilisez le centre d'aide pour trouver la bonne page

## 5. Gestion des produits

### À quoi ça sert

Le module `Produits` permet de créer et maintenir le catalogue des articles vendus ou stockés.

### Ce que vous pouvez faire

- créer un article
- modifier un article
- consulter la quantité disponible
- consulter la fiche stock
- importer des produits par fichier CSV ou Excel

### Informations principales d'un produit

- `nom`
  Explication: nom affiché dans les achats, ventes et rapports.
- `SKU`
  Explication: référence interne unique de l'article.
- `code-barres`
  Explication: utile pour la lecture rapide ou l'identification.
- `unité`
  Explication: pcs, kg, litre, mètre, etc.
- `coût moyen`
  Explication: coût utilisé pour le suivi du stock.
- `prix de vente`
  Explication: prix proposé au client.
- `marge`
  Explication: indicateur commercial.
- `seuil de réapprovisionnement`
  Explication: niveau à partir duquel le produit devient critique.

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

### À retenir

- si `unit_code` est absent, l'unité par défaut peut être utilisée
- si `stock` est renseigné, la quantité est ajoutée dans l'entité choisie
- vérifiez toujours les références avant un import massif

## 6. Fiche stock produit

### À quoi ça sert

La fiche stock donne une vue complète sur la situation d'un article.

### Ce que vous y voyez

- `stock total`
  Explication: quantité totale disponible sur les emplacements visibles.
- `seuil de réapprovisionnement`
  Explication: quantité minimale attendue.
- `valeur du stock`
  Explication: estimation basée sur les quantités et le coût.
- `stock par emplacement`
  Explication: détail dépôt par dépôt ou magasin par magasin.
- `historique des mouvements`
  Explication: entrées, sorties, ajustements et variations successives.

### Présentation des quantités

- `3`
  Explication: affichage simple pour une quantité entière.
- `2.5`
  Explication: affichage décimal seulement si nécessaire.

### Utilité pratique

- comprendre où se trouve le stock
- expliquer une rupture
- vérifier l'effet d'un achat ou d'une vente

## 7. Fournisseurs et clients

### Fournisseurs

Le module `Fournisseurs` sert à:

- enregistrer les partenaires d'achat
- conserver leurs coordonnées
- les réutiliser dans les commandes d'achat

### Clients

Le module `Clients` sert à:

- enregistrer les acheteurs récurrents
- suivre les informations utiles à la vente
- retrouver rapidement un client dans l'historique

### Conseil

- créez vos fournisseurs et clients avant les opérations réelles
- utilisez des fiches claires pour éviter les doublons

## 8. Achats

### À quoi ça sert

Le module `Achats` permet de faire entrer du stock dans le système.

### Ce que vous pouvez faire

- créer une commande d'achat
- modifier une commande
- visualiser les lignes commandées
- suivre les quantités reçues
- imprimer le bon de commande

### Ce qu'il faut vérifier

- le bon fournisseur
- les bonnes quantités
- le bon emplacement de réception
- le bon prix d'achat

### Pourquoi c'est important

- un achat bien saisi alimente correctement le stock
- une erreur d'achat fausse la quantité et la valeur du stock

## 9. Ventes

### À quoi ça sert

Le module `Ventes` permet d'enregistrer les sorties de stock liées aux ventes.

### Ce que vous pouvez faire

- ajouter des articles à vendre
- choisir le magasin ou dépôt de sortie
- vérifier le stock disponible
- enregistrer la vente
- imprimer le document associé
- traiter certains ajustements après vente si l'option existe

### Avant validation

- vérifiez la disponibilité réelle du stock
- contrôlez les quantités saisies
- confirmez les montants et remises
- choisissez le bon emplacement de sortie

### Résultat attendu

- le stock diminue correctement
- la vente apparaît dans les rapports
- le mouvement devient traçable

## 10. Mouvements de stock

### À quoi ça sert

Le journal des mouvements sert à comprendre ce qui a modifié le stock.

### Types de mouvements visibles

- `entrées`
  Explication: ajout de stock, souvent après un achat ou un ajustement positif.
- `sorties`
  Explication: diminution de stock, souvent après une vente ou un ajustement négatif.
- `ajustements`
  Explication: correction manuelle ou régularisation.
- `transferts`
  Explication: déplacement entre deux emplacements.

### Quand utiliser ce module

- quand une quantité semble incohérente
- quand vous voulez vérifier une variation
- quand vous devez auditer l'historique d'un article

## 11. Transferts de stock

### À quoi ça sert

Le module `Transferts` permet de déplacer du stock d'un emplacement vers un autre.

### Étapes générales

1. choisir l'emplacement source
2. choisir l'emplacement de destination
3. sélectionner les articles
4. saisir les quantités
5. confirmer le transfert

### Ce qu'il faut vérifier

- le stock disponible à la source
- la bonne destination
- les bonnes quantités

### Effet attendu

- le stock baisse dans l'emplacement source
- le stock augmente dans l'emplacement de destination

## 12. Inventaires

### À quoi ça sert

Le module `Inventaires` compare le stock système et le stock réellement compté.

### Ce que vous pouvez faire

- créer un inventaire manuel
- importer un inventaire par fichier
- analyser les écarts
- régulariser les différences

### Ce que le module aide à détecter

- les manquants
- les surplus
- les erreurs de saisie
- les oublis de mouvement

### Bon moment pour faire un inventaire

- à la fin d'une période
- après un écart constaté
- avant une clôture interne

## 13. Dépenses

### À quoi ça sert

Le module `Dépenses` enregistre les charges de fonctionnement de l'entreprise.

### Exemples de dépenses

- transport
- loyer
- maintenance
- fournitures

### Utilité

- améliorer le suivi financier
- mesurer le coût réel d'exploitation
- alimenter les rapports

## 14. Rapports

### Types de rapports disponibles

- `rapport financier`
  Explication: vision globale des montants, coûts, stock et résultats.
- `rapport des ventes`
  Explication: détail des quantités vendues, montants et lignes commerciales.

### Ce que vous pouvez suivre

- les quantités vendues
- les montants de ventes
- la valeur du stock
- les marges et coûts

### Utilité pratique

- piloter l'activité
- repérer les produits performants
- identifier des écarts ou des baisses

## 15. Utilisateurs et sécurité

### Ce que permet le module `Utilisateurs`

- créer un utilisateur
- attribuer un rôle
- affecter un emplacement si nécessaire
- modifier un compte existant

### Bonnes pratiques de sécurité

- ne partagez pas les mots de passe
- changez les accès des comptes inutilisés
- limitez les droits selon le rôle réel de l'utilisateur
- utilisez le code secret de suppression avec prudence

### Important

- tous les utilisateurs ne doivent pas avoir un accès global
- plus les droits sont élevés, plus les actions sont sensibles

## 16. Paramètres société

### À quoi ça sert

Le module `Paramètres société` centralise les réglages généraux.

### Ce que vous pouvez configurer

- les informations de l'entreprise
- certains réglages de présentation
- les seuils globaux de stock bas
- les éléments visibles sur les documents

### Conseil

- complétez cette page avant l'utilisation quotidienne
- gardez les informations toujours à jour

## 17. Sauvegardes et santé système

### Sauvegardes

Le module `Sauvegardes` permet de récupérer des instantanés de données selon la configuration active.

### Santé système

Le module `Santé système` aide à vérifier:

- l'état général de l'application
- certains services ou dépendances
- le stockage et la configuration

### Pourquoi c'est utile

- prévenir les pannes ou incohérences
- contrôler rapidement l'état technique du système

## 18. Corbeille

### À quoi ça sert

La corbeille permet de retrouver certains éléments supprimés sans perte immédiate.

### Ce que vous pouvez faire

- restaurer un enregistrement supprimé
- contrôler les suppressions récentes
- éviter des pertes accidentelles

### Bon usage

- vérifiez toujours la corbeille avant de recréer une fiche supprimée

## 19. Bonnes pratiques d'utilisation

### Ordre conseillé

- créez d'abord les unités, emplacements, fournisseurs et clients
- ajoutez les produits avant les opérations de stock
- utilisez les achats pour alimenter le stock réel
- faites les ventes uniquement depuis le bon emplacement
- contrôlez régulièrement les alertes de stock bas
- réalisez des inventaires périodiques
- consultez les rapports pour suivre la performance

### Erreurs à éviter

- vendre depuis le mauvais emplacement
- importer des produits sans vérifier les colonnes
- ignorer les alertes de stock bas
- modifier des données sans vérifier l'impact stock

## 20. Résolution rapide des problèmes

### Impossible d'accéder à une page

Causes possibles:

- utilisateur non connecté
- e-mail non vérifié
- rôle insuffisant
- emplacement non autorisé

### Quantité insuffisante

À vérifier:

- l'emplacement sélectionné
- la quantité réellement disponible
- les transferts déjà effectués
- les ventes déjà enregistrées

### Utilisateur introuvable pour l'inscription

Explication:

- l'inscription publique est fermée
- un `owner` ou `manager` autorisé doit créer le compte depuis `Utilisateurs`

## 21. Conclusion

### Pour bien démarrer

1. connectez-vous
2. configurez l'entreprise
3. créez les unités et emplacements si besoin
4. ajoutez les produits
5. enregistrez les achats
6. effectuez les ventes
7. surveillez les rapports et inventaires

### Idée simple à retenir

- si le stock est faux, vérifiez les achats, ventes, transferts et inventaires
- si l'accès est bloqué, vérifiez le rôle et l'emplacement autorisé
