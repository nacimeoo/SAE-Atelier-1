# NRV_laghezali_meziani_valeurmaselli
### Membres du groupe

LAGHEZALI Nacime

MEZIANI Fahim

VALEUR--MASELLI Marius


# Rendu Intermediare

Les maquettes d'interface, le modèle du domaine métier ainsi que les scenario d'usage se trouve a la racine du projet dans un dossier documents

## Installation
 
1. Cloner le dépôt :
```bash
git clone https://github.com/nacimeoo/SAE-Atelier-1.git
cd SAE-Atelier-1/NRV
```

2. Créer le fichier `.env` à la racine de `NRV/`  :
```env
POSTGRES_DB=nrv
POSTGRES_USER=root
POSTGRES_PASSWORD=1234
ADMINER_DEFAULT_SERVER=sql.db
```

3. Lancer les conteneurs :
```bash
docker compose up -d
```

## Services
 
| Service | URL |
|---|---|
| Front | http://localhost:7081 |
| API | http://localhost:7080 |
| Adminer | http://localhost:7082 |


## Comptes de test
 
| Rôle | Email | Mot de passe |
|---|---|---|
| Gestionnaire | admin@nrv.net | password |
| Client | gonzalez.eugene@example.net | password |


## Fonctionnalités et répartition des tâches
 
| N° | Fonctionnalité | Réalisé par |
|---|---|---|
| | **Programme** | |
| 1 | Liste des spectacles (titre, date, horaire, image) | Nacime |
| 2 | Détail d'une soirée (nom, thématique, date, horaire, lieu, tarifs, spectacles) | Fahim |
| 3 | Clic sur un spectacle, qui affiche la soirée correspondante | Marius |
| 4 | Filtre par date et par style musical | Nacime |
| | **Achat de billets** | |
| 5 | Ajout de billets d'une soirée dans le panier | Nacime |
| 6 | Consultation du panier et montant total | Fahim |
| 7 | Validation du panier, qui devient une commande | Marius |
| 8 | Paiement simulé de la commande | Fahim |
| 9 | Création des billets imprimables | Nacime |
| | **Connexion** | |
| 10 | Inscription et connexion | Nacime |
| | **Back-office** | |
| 11 | Jauge par soirée (places vendues) et recette | Nacime |
| | **Autres tâches** | |
| 12 | Déploiement avec docker compose | Fahim |
| 13 | Front global de l'application | Marius |


## Front : compilation SCSS
 
Le CSS compilé est versionné.
 
```bash
npx sass --watch front/public/scss/style.scss front/public/css/style.css
```