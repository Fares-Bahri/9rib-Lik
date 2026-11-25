# Gestion des fermes et produits (PHP MVC)

Application PHP MVC (PDO, sessions, rôles `user` / `admin`) avec CRUD complet sur **Ferme** et **Produit** (1 ferme -> N produits).

## Installation (XAMPP)
1. Copier le dossier `ferme-ui` dans `C:\xampp\htdocs\`.
2. Démarrer **Apache** et **MySQL** dans XAMPP.
3. phpMyAdmin > onglet **SQL** > coller le contenu de `database.sql` > Exécuter (base `gestion_projet` : ajoute `role` à `utilisateur` et crée `fermes` et `produits`).
4. Si besoin, adapter l'accès MySQL dans `config/database.php`.
5. Ouvrir http://localhost/ferme-ui/index.php

## Comptes de test
- Admin : `admin@ferme.tn` / `Admin@1234`
- Utilisateur : s'inscrire via le formulaire (rôle `user` forcé).

## Structure MVC
- `models/` : UserModel, FermeModel, ProduitModel (attributs privés + getters/setters)
- `controllers/` : AuthController, FermeController, ProduitController (SQL préparé, validation, CSRF, rôles)
- `views/` : HTML uniquement (`layout.php` front, `layout_admin.php` back)
- `index.php` : point d'entrée unique (`?action=...`)

## Sécurité
password_hash, requêtes préparées, htmlspecialchars, jeton CSRF, session_regenerate_id, cookie HttpOnly/SameSite, expiration 30 min, en-têtes HTTP, contrôle de rôle sur chaque page admin.

## Entités
- **Ferme** : id, nom, emplacement, aire, type_agriculture, description
- **Produit** : id, nom, categorie, prix, quantite_stock, unite, date_recolte, ferme_id
