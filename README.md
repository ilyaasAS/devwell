# 🏰 Devwell – Plateforme E-Commerce Souveraine (README Golden Master)

Devwell est une plateforme e-commerce souveraine de classe industrielle, bâtie sur **Symfony 7** et **PHP 8.2+**. Elle est conçue pour une résilience maximale, une indépendance totale vis-à-vis des tiers et une élégance opérationnelle absolue. 

La plateforme est entièrement conteneurisée avec **Docker + Docker Compose**, protégée en production par **Caddy** pour la gestion HTTP/HTTPS, et pensée pour être portable, auditable et sécurisée dès sa conception.

> **Philosophie directrice : L'Ingénierie Souveraine**
> Aucune dépendance SaaS cachée, propriété totale des données, infrastructure déterministe et contrôles de sécurité proactifs. Devwell peut être cloné, initialisé, exploité, sauvegardé et redéployé sur n'importe quel hôte Linux compatible (ou VM cloud) disposant de Docker.

---

## ▸ 1. Vision Souveraine et Vue d'Ensemble

### 1.1 L'Ingénierie Souveraine
L'Ingénierie Souveraine dans Devwell signifie :
* **Indépendance totale vis-à-vis des fournisseurs** : Tous les composants critiques (application web, MariaDB, MongoDB, capture de mail, reverse proxy) fonctionnent comme des services Docker. Aucune dépendance SaaS payante ou PaaS opaque n'est intégrée au chemin critique.
* **Portabilité absolue via Docker** : Un seul `docker-compose.yml` pour le développement local et un `docker-compose.prod.yml` durci pour la production. Le même Dockerfile construit l'image de l'application pour tous les environnements.
* **Architecture axée sur la sécurité** : Exposition directe nulle des bases de données internes en production (réseau bridge privé). Points de terminaison (endpoints) de santé protégés par des en-têtes secrets partagés. Intégration de l'IA (Gemini) encapsulée dans un service dédié.

### 1.2 Aperçu Fonctionnel de Haut Niveau
* **Fonctionnalités E-commerce** : Catalogue (produits, catégories, avis, prix), flux de panier et de paiement, gestion des utilisateurs, back-office administrateur, gestion des webhooks Stripe.
* **Assistant de vente augmenté par l'IA** : Endpoint `/api/chat` fournissant une assistance via **Gemini 2.5 Flash Lite**. Contexte RAG basé sur les derniers produits et avis.
* **Plan Opérationnel** : Endpoint de santé `/api/health`. Scripts de sauvegarde complets pour MariaDB et MongoDB. Script de déploiement sans interruption de service (Zero-downtime).

---

## ▸ 2. Analyse Approfondie du Projet (Le Cœur)

### 2.1 Séparation de l'Architecture – Moteur UX vs APIs Stateless
L'application Symfony est structurée autour d'une séparation claire des responsabilités :
* **Frontend (Moteur UX)** – `App\Controller\Frontend\*` : Gère toutes les pages orientées utilisateur. Optimisé pour une UX basée sur les sessions, des vues rendues côté serveur et des routes respectueuses du SEO. Design implémenté avec **Tailwind CSS**.
* **Couche API (Services Stateless)** – `App\Controller\Api\*` : Expose des points de terminaison sans état consommables par des machines (ChatApiController, HealthCheckController).

### 2.2 Stratégie de Données – Hybride MariaDB + MongoDB
* **MariaDB (Cœur Relationnel / Transactionnel)** : Utilisé pour les entités critiques du commerce (utilisateurs, produits, commandes, paiements).
* **MongoDB (Document / Log / Contexte RAG)** : Utilisé pour les documents contextuels et les connaissances non relationnelles destinées à l'assistant IA.

### 2.3 Intelligence Artificielle – Intégration Gemini 2.5 Flash Lite
L'intégration de l'IA est encapsulée dans `App\Service\GeminiService` :
* **Construction du contexte RAG** : Compose le contexte à partir des 10 derniers produits et des 10 derniers avis (tronqués à 100 caractères pour la confidentialité).
* **Minimisation des données personnelles (PII)** : Un prompt système strict interdit à l'assistant de divulguer des informations personnelles ou financières internes.
* **Contrôles Anti-DoS** : Rejet des messages de plus de 1500 caractères.

---

## ▸ 3. Stack Technique et Standards d'Ingénierie

### 3.1 Stack Principale

| Couche | Technologie | Rôle |
| :--- | :--- | :--- |
| **Langage** | PHP 8.2+ | Runtime backend principal |
| **Framework** | Symfony 7 | Framework HTTP, DI, routage, sécurité |
| **Design Frontend** | Tailwind CSS | CSS utilitaire pour une UI rapide |
| **Serveur HTTP** | Apache | php:8.2-apache pour Symfony |
| **Reverse Proxy** | Caddy | Terminaison TLS, automatisation HTTPS |
| **BDD Relationnelle** | MariaDB 10.11 | Transactions, modèle de domaine |
| **BDD Document** | MongoDB | Contexte, logs, documents flexibles |
| **Conteneurisation** | Docker | Paquetage et isolation |

---

## ▸ 4. Installation et Démarrage (Protocole Dev)

### ⚠️ Prérequis Système
Avant de commencer, assurez-vous que la machine cible dispose de :
* **Git** : Pour cloner l'empire.
* **Docker Desktop** : Le moteur de l'infrastructure.
* **Make** : Pour piloter le projet (natif sur macOS).

### 4.1 Lancement de la Stack (Méthode Automatisée - Makefile)

Sur macOS, le Makefile est le cerveau de l'installation. Il gère tout, de la création des fichiers de configuration au peuplement de la base de données.

### 1. Récupération du projet
git clone https://github.com/ilyaasAS/devwell.git
cd devwell

### 2. Installation souveraine
make install

Le comportement intelligent du Makefile :

Initialisation : Il détecte l'absence de .env.local et le crée automatiquement pour toi (cp .env .env.local).

Sécurité : Il s'arrête et t'invite à remplir tes clés (Stripe, Gemini) avant de continuer.

Automatisation : Une fois les clés saisies, relancer make install déclenche l'ouverture de Docker, l'attente de 30s, le build, la compilation Tailwind et l'injection des fixtures.


### 4.2 Décomposition Manuelle (Flux de secours)

Si tu décides de ne pas utiliser l'automate, voici la séquence exacte à reproduire manuellement :

### 1. Configuration manuelle
cp .env .env.local
# -> Remplir Stripe, Gemini et API ici.

### 2. Réveil du moteur Docker et construction
open -a Docker && sleep 30 && docker compose up -d --build

### 3. Construction de la façade visuelle (Assets)
docker compose exec app php bin/console tailwind:build

### 4. Peuplement du royaume (Données de test)
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

### 4.3 Ressources de Test et Accès (Dossier `/docs`)
Une fois l'installation terminée (`make install`), le projet est prêt à l'emploi. Pour faciliter vos tests, des ressources sont disponibles à la racine dans le dossier `docs/` :

* **Identifiants Utilisateurs (`docs/compte_test`)** : Contient les emails et mots de passe des comptes créés par les fixtures (Profils Admin et Clients).
* **Paiements de Test (`docs/Carte_test_stripe`)** : Contient les numéros de cartes bancaires fournis par Stripe pour simuler des transactions réussies ou échouées en mode développement.

---

▸ 5. Déploiement Souverain en Production (Oracle Cloud)
Le passage en production obéit à la règle stricte du "Zéro Secret en Ligne". Le fichier .env public ne contient aucune donnée sensible. Le pouvoir réside dans le .env.local du serveur de production.

5.1 Préparation de l'Infrastructure Matérielle
Instance : Créer un VPS (ex: Ampere A1 sur Oracle Cloud) sous Ubuntu.

Réseau : Ouvrir les ports 80 (HTTP) et 443 (HTTPS) dans les règles de sécurité du pare-feu Cloud.

Moteur : Se connecter via SSH et installer les dépendances :

sudo apt update && sudo apt upgrade -y
sudo apt install docker.io docker-compose-v2 git -y
sudo usermod -aG docker $USER

5.2 Le Coffre-Fort (Configuration Isolée)
Cloner le projet sur le serveur, puis créer manuellement le fichier .env.local :

git clone https://github.com/ilyaasAS/devwell.git
cd devwell
nano .env.local

Insérer impérativement APP_ENV=prod, APP_DEBUG=0, et les clés d'API (Stripe Live, Gemini, etc.). Sauvegarder et quitter.

5.3 La Compilation de Guerre (Automatisée ou Manuelle)
Option A : L'Automate Stratégique (Recommandé)
Exécuter la commande d'architecture centrale :

make install

En détectant APP_ENV=prod, le Makefile va automatiquement minifier extrêmement Tailwind CSS (--minify), figer les assets statiques (asset-map:compile) et bloquer l'injection des Fixtures.

Option B : Décomposition Manuelle (Contrôle Absolu)
Si l'automate n'est pas utilisé, voici la séquence stricte à exécuter pour forger l'environnement de production étape par étape :

# 1. Démarrage des moteurs et construction des images
docker compose up -d --build

# 2. Installation des dépendances PHP (Optimisées)
docker compose exec app composer install --no-interaction --optimize-autoloader

# 3. Minification extrême du design pour la vélocité maximale
docker compose exec app php bin/console tailwind:build --minify

# 4. Figeage des assets statiques (AssetMapper)
docker compose exec app php bin/console asset-map:compile

# 5. Création de la structure de la base de données (Tables vides)
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

(Aucune commande de fixtures n'est exécutée ici pour préserver la base).

5.4 Le Sacre (Élévation des Privilèges)
La base de données étant vierge, la création du compte Administrateur se fait par injection SQL après une inscription légitime :

S'inscrire sur le site web public avec l'email du dirigeant.

Élever les privilèges via le terminal du serveur (SSH) :

docker exec -it devwell_app php bin/console dbal:run-sql "UPDATE user SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'ton-vrai-email@devwell.com';"

---

##  ▸ 6. Sécurité et Infrastructure

Isolation "Zero-Trust" : En production, les bases de données sont invisibles de l'extérieur. Seul le conteneur app communique avec database:3306 et mongodb:27017 via le réseau privé.

Surveillance /api/health : Endpoint protégé par l'en-tête X-HEALTH-TOKEN. Effectue des tests de connexion réels sur les deux bases de données avant de répondre.

---

## ▸ 7. Opérations Souveraines (Résilience)

### 7.1 Protocole de Sauvegarde – .docker/backup.sh

Sauvegardes adaptées aux tâches Cron :

MariaDB : Dump SQL via mysqldump --skip-ssl sans allocation de TTY.

MongoDB : Archive binaire via mongodump --archive.

Sorties streamées vers le dossier backups/ de l'hôte.

### 7.2 Déploiement sans interruption – .docker/deploy.sh
Flux optimisé : Mise à jour Git → Reconstruction images → Recréation conteneurs (detatched) → Nettoyage cache prod.

---

## ▸ 8. Référence Rapide API

| Endpoint | Méthode | Description | Sécurité |
| :--- | :--- | :--- | :--- |
| `/` | GET | Page d'accueil | Session / Public |
| `/api/chat` | POST | Assistant de vente IA | JSON Body |
| `/api/health` | GET | Check de santé infra | Header X-HEALTH-TOKEN |

---

## ▸ 9. Conclusion

L'approche d'Ingénierie Souveraine de Devwell produit une plateforme e-commerce résiliente, sécurisée par conception et augmentée par l'IA tout en respectant la confidentialité des données.