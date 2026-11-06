# Art de Rue

## Prérequis

- PHP **8.4** ou plus (extensions `pdo_mysql`, `ctype`, `iconv`, `intl`)
- Composer
- **MySQL ou MariaDB** (les migrations sont écrites pour MySQL/MariaDB)
- Optionnel : [Symfony CLI](https://symfony.com/download)
- Une connexion internet (la carte Leaflet est chargée depuis un CDN)

## Installation

```bash
git clone https://github.com/keenanblet/EvaluationSymfony.git
cd EvaluationSymfony
cd ArtDeRue

composer install
php bin/console importmap:install
```

### Base de données

Le fichier `.env` versionné contient une URL par défaut qui ne correspond pas au projet.
Créer un fichier **`.env.local`** à la racine avec ses propres identifiants MySQL/MariaDB :

```dotenv
# MariaDB
DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@127.0.0.1:3306/ArtDeRue?serverVersion=11.8.6-MariaDB&charset=utf8mb4"

# ou MySQL 8
# DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@127.0.0.1:3306/ArtDeRue?serverVersion=8.0.32&charset=utf8mb4"

APP_SECRET=une_chaine_aleatoire_quelconque
```

Puis créer la base, jouer les migrations et charger les données de démo :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

### Lancer le serveur

```bash
symfony server:start

```

Le site est alors accessible sur https://127.0.0.1:8000.

## Comptes de démonstration

Tous les comptes ont le mot de passe **`password`**.

| Email                  | Rôle            | Droits                                                    |
|------------------------|-----------------|-----------------------------------------------------------|
| `user@artderue.fr`     | `ROLE_USER`     | Consultation des œuvres et des artistes                   |
| `benevole@artderue.fr` | `ROLE_BENEVOLE` | + ajout d'œuvres (avec photo et lieu sur la carte)        |
| `admin@artderue.fr`    | `ROLE_ADMIN`    | + modification / suppression, artistes, techniques, utilisateurs, dashboard admin |

Hiérarchie des rôles : `ROLE_ADMIN` > `ROLE_BENEVOLE` > `ROLE_USER`.

## Pages principales

| URL          | Description                                  | Accès           |
|--------------|----------------------------------------------|-----------------|
| `/`          | Liste des œuvres                             | Tout le monde   |
| `/{id}`      | Détail d'une œuvre (photos, artistes, lieu)  | Tout le monde   |
| `/new`       | Ajouter une œuvre                            | Bénévole, admin |
| `/artiste`   | Liste des artistes                           | Tout le monde   |
| `/login`     | Connexion                                    | —               |
| `/menu`      | Menu                                         | —               |
| `/admin`     | Dashboard d'administration                   | Admin           |
| `/technique` | Gestion des techniques                       | Admin           |
| `/user`      | Gestion des utilisateurs                     | Admin           |

## Données de démo

Les fixtures créent :

- 6 utilisateurs (dont les 3 comptes ci-dessus) ;
- les techniques (bombe aérosol, pochoir, collage, mosaïque, fresque, sticker) ;
- une œuvre réelle : la fresque d'**Ador & Katra** au **Hangar à Bananes** (Nantes), avec sa photo.

Les photos envoyées via le formulaire sont stockées dans `public/uploads/photos/`.

## Structure

```
src/
├── Controller/   Contrôleurs (œuvres, artistes, techniques, utilisateurs, admin, login)
├── DataFixtures/ Données de démo
├── Entity/       Oeuvre, Artiste, Lieu, Technique, Photos, User
├── Form/         Formulaires
├── Migrations/   Migrations Doctrine
├── Repository/
├── Security/     Authentificateur du formulaire de connexion
└── Service/      FileUploader (upload des photos)
templates/        Vues Twig
```
