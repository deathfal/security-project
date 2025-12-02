# Cahier des Charges pour le Projet "Cosmic Bazaar"

## 1. Introduction
Le projet "Cosmic Bazaar" est une application web de commerce électronique permettant aux utilisateurs d'acheter des produits cosmiques. Le projet utilise le framework Symfony, Tailwind CSS pour le design, et Webpack Encore pour la gestion des assets. L'application est conteneurisée avec Docker et utilise PostgreSQL comme base de données.

## 2. Objectifs
- Développer une plateforme de commerce électronique fonctionnelle.
- Permettre aux utilisateurs de parcourir, rechercher et acheter des produits.
- Gérer les utilisateurs, les commandes, les paiements et les avis.
- Fournir une interface d'administration pour gérer les produits, les catégories et les commandes.

## 3. Fonctionnalités

### 3.1. Frontend Utilisateur
- **Page d'accueil** : Présentation des produits en vedette et des promotions.
- **Catalogue de produits** : Liste des produits avec filtres par catégorie et prix.
- **Recherche de produits** : Fonctionnalité de recherche pour trouver des produits spécifiques.
- **Détail du produit** : Affichage des détails du produit, des avis et des options d'achat.
- **Panier** : Gestion du panier d'achat avec ajout, suppression et modification des quantités.
- **Paiement** : Formulaire de paiement sécurisé avec gestion des informations de livraison et de paiement.
- **Historique des commandes** : Affichage des commandes passées par l'utilisateur.
- **Avis** : Possibilité de laisser des avis sur les produits achetés.

### 3.2. Backend Administration
- **Gestion des produits** : Ajouter, modifier et supprimer des produits.
- **Gestion des catégories** : Ajouter, modifier et supprimer des catégories de produits.
- **Gestion des commandes** : Suivi et gestion des commandes des utilisateurs.
- **Gestion des utilisateurs** : Gestion des comptes utilisateurs et des rôles.

## 4. Technologies Utilisées
- **Framework** : Symfony
- **Base de données** : PostgreSQL
- **Frontend** : Tailwind CSS, Alpine.js
- **Gestion des assets** : Webpack Encore
- **Conteneurisation** : Docker
- **Serveur web** : Caddy avec FrankenPHP

## 5. Structure du Projet
- **src/** : Contient les contrôleurs, entités, répertoires et services.
- **templates/** : Contient les templates Twig pour les vues.
- **public/** : Contient les fichiers publics comme les CSS, JS et images.
- **config/** : Contient les fichiers de configuration de Symfony.
- **migrations/** : Contient les fichiers de migration de la base de données.
- **assets/** : Contient les fichiers sources CSS et JS.
- **docker/** : Contient les fichiers de configuration Docker.

## 6. Exigences Fonctionnelles
- **Inscription et Connexion** : Les utilisateurs doivent pouvoir s'inscrire et se connecter.
- **Gestion du Panier** : Les utilisateurs doivent pouvoir ajouter des produits au panier, modifier les quantités et supprimer des produits.
- **Processus de Paiement** : Les utilisateurs doivent pouvoir entrer leurs informations de livraison et de paiement pour finaliser l'achat.
- **Historique des Commandes** : Les utilisateurs doivent pouvoir consulter l'historique de leurs commandes.
- **Avis sur les Produits** : Les utilisateurs doivent pouvoir laisser des avis sur les produits achetés.

## 7. Exigences Non Fonctionnelles
- **Sécurité** : Utilisation de HTTPS pour sécuriser les communications. Gestion des rôles et permissions pour l'accès aux différentes parties de l'application.
- **Performance** : Optimisation des requêtes SQL et des assets pour un chargement rapide des pages.
- **Scalabilité** : Utilisation de Docker pour faciliter le déploiement et la scalabilité de l'application.
- **Accessibilité** : Respect des standards d'accessibilité pour permettre l'utilisation de l'application par tous les utilisateurs.

## 8. Déploiement
- **Environnement de Développement** : Utilisation de Docker pour créer un environnement de développement cohérent.
- **Environnement de Production** : Déploiement sur un serveur avec Docker Compose. Utilisation de Caddy pour la gestion des certificats SSL et le reverse proxy.
## 9. Gestion de Projet
- **Suivi des Tâches** : Utilisation de GitHub Issues pour le suivi des tâches et des bugs.
- **Gestion des Versions** : Utilisation de Git pour le contrôle de version.
- **Documentation** : Documentation du code et des fonctionnalités dans le répertoire `docs/`.

## 10. Conclusion
Le projet "Cosmic Bazaar" vise à fournir une plateforme de commerce électronique complète et moderne, utilisant les meilleures pratiques de développement web. Le cahier des charges décrit les fonctionnalités, les technologies et les exigences pour atteindre cet objectif.