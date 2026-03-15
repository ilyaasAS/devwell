# Audit de conformité "Production Ready" et portabilité – Devwell

**Date :** 2026-03  
**Périmètre :** Lecture seule – validation déploiement machine vierge / VPS, standards industrie.  
**Exception :** Contenu client (Twig, messages flash, labels) reste en français.

---

## 1. Naming & standard technique (International English)

### 1.1 Architecture (fichiers, dossiers, namespaces)

| Élément | Statut | Détail |
|--------|--------|--------|
| **Namespaces** | ✅ | `App\Controller`, `App\Entity`, `App\Service`, etc. – PSR-4 et Symfony. |
| **Dossiers src/** | ✅ | Controller (Admin, Api, Auth, Frontend, Webhook), Entity, Form, Repository, Service, Document, Twig, Serializer, Security – cohérent. |
| **Fichiers PHP** | ✅ | PascalCase pour classes, suffixes (Controller, Type, Service, Repository) conformes. |

### 1.2 Code PHP (entités, propriétés, méthodes, variables, services)

| Élément | Statut | Détail |
|--------|--------|--------|
| **Entités** | ✅ | User, Order, OrderItem, Cart, Product, Category, Contact, ResetPasswordRequest – anglais. |
| **Propriétés / getters-setters** | ✅ | firstName, lastName, email, subject, message, response, isResponded, orderItems, etc. |
| **Services** | ✅ | GeminiService, CartService, AiServiceInterface – anglais. |
| **Repositories** | ✅ | UserRepository, OrderRepository, CartRepository, etc. |
| **Commentaires** | ⚪ | Nombreux commentaires en français (hors périmètre "naming" stricte). |

### 1.3 URLs et routes

| Élément | Statut | Détail |
|--------|--------|--------|
| **Paths** | ✅ | `/login`, `/register`, `/products`, `/cart`, `/checkout`, `/reviews`, `/admin/users`, `/api/chat`, `/api/health`, `/webhook/stripe`, etc. – anglais. |
| **Noms de routes** | ✅ | `app_login`, `app_register`, `app_product_details`, `cart_view`, `checkout`, `api_chat` – cohérents et en anglais. |
| **Incohérence route** | ❌ | **CartController** (lignes 37 et 43) : `redirectToRoute('product_details', ...)` alors que la route réelle est **`app_product_details`**. Risque **RouteNotFoundException** au redirect après ajout au panier / erreur de quantité. |

---

## 2. Dockérisation & simulation VPS

### 2.1 Isolement prod

| Élément | Statut | Détail |
|--------|--------|--------|
| **docker-compose.prod.yml** | ✅ | Service `app` : **`volumes: []`** présent explicitement. Aucun bind mount → conteneur 100 % basé sur l’image. |
| **env_file** | ✅ | `[.env, .env.local]` – pas d’override d’env par le YAML pour APP_ENV. |

### 2.2 Dockerfile

| Élément | Statut | Détail |
|--------|--------|--------|
| **Chaîne de build** | ✅ | Ordre : COPY → rm var/ → mkdir var/cache, var/log → chown www-data + chmod 775 → composer dump-autoload → **tailwind:build** → **asset-map:compile** → **cache:clear --env=prod** → chown var/. |
| **Permissions** | ✅ | `chown -R www-data:www-data /var/www/html` puis `chown -R www-data /var/www/html/var` en fin de build. |
| **Optimisation** | ⚪ | `composer install` sans `--no-dev` ; `tailwind:build` sans `--minify` dans le Dockerfile (optionnel pour image prod plus légère). |

### 2.3 Entrypoint

| Élément | Statut | Détail |
|--------|--------|--------|
| **Attente BDD** | ✅ | Boucle `doctrine:query:sql "SELECT 1"` jusqu’à 60 tentatives, 1 s d’intervalle. |
| **Migrations** | ✅ | `doctrine:migrations:migrate --no-interaction` après attente. |
| **Permissions** | ✅ | `chown -R www-data:www-data /var/www/html/var /var/www/html/public` avant `apache2-foreground`. |
| **Composer si besoin** | ✅ | Si `vendor/autoload.php` absent → `composer install` (cas volume monté sans vendor). |

---

## 3. Configuration & automatisation

### 3.1 Makefile ("Tour de Contrôle")

| Élément | Statut | Détail |
|--------|--------|--------|
| **Détection env** | ✅ | APP_ENV lu depuis .env.local puis .env, défaut dev ; COMPOSE_FILE et COMPOSE dérivés. |
| **install** | ✅ | Vérif .env puis .env.local (création + message si absent), open Docker + sleep 30, up --build, composer install, assets (prod: minify + asset-map), chown en prod, migrate, fixtures (bloquées en prod). |
| **Portabilité Mac vierge** | ✅ | `make install` suffit après création/remplissage de .env.local. |
| **Portabilité Linux / VPS** | ⚠️ | `open -a Docker` et `sleep 30` sont orientés macOS. Sur Linux, Docker doit être déjà installé et le daemon démarré ; l’étape "Lancement Docker" est sans effet. |

### 3.2 .env.example

| Élément | Statut | Détail |
|--------|--------|--------|
| **Variables couvertes** | ✅ | APP_ENV, APP_SECRET, APP_DEBUG, TRUSTED_PROXIES, DATABASE_URL, DB_USER, DB_PASSWORD, DB_NAME, DB_ROOT_PASSWORD, MAILER_DSN, STRIPE_PUBLIC_KEY, STRIPE_SECRET_KEY, STRIPE_WEBHOOK_SECRET, MONGODB_URL, MONGODB_DB, GOOGLE_GEMINI_API_KEY, HEALTH_CHECK_TOKEN. |
| **Commentaires** | ✅ | En anglais, courte description en tête. |

### 3.3 services.yaml

| Élément | Statut | Détail |
|--------|--------|--------|
| **Gemini** | ✅ | `default_gemini_api_key: ''` et `env(default:default_gemini_api_key:GOOGLE_GEMINI_API_KEY)` – pas de crash si variable absente. |
| **Injections** | ✅ | AiServiceInterface → GeminiService ; ReviewRepository en factory ODM. |

---

## 4. Intégration IA (Gemini)

| Élément | Statut | Détail |
|--------|--------|--------|
| **RAG** | ✅ | `buildRagContext()` : produits (nom, prix, description) + avis (note, commentaire) injectés dans le prompt. |
| **Gestion d’erreurs** | ✅ | Clé vide, TransportException, HTTP ≠ 200, payload inattendu, Throwable → log + message de fallback en français, pas de crash. |
| **Instruction de langue** | ⚠️ | `systemInstruction` décrit le rôle en français mais **n’impose pas explicitement** "Réponds uniquement en français de manière professionnelle". Le modèle peut déjà répondre en français par mimétisme ; pour garantir la consigne, ajouter une phrase explicite dans l’instruction. |

---

## 5. Cohérence globale (vestiges, orphelins, français "moteur")

| Élément | Statut | Détail |
|--------|--------|--------|
| **Fichiers orphelins** | ✅ | Aucun contrôleur/formulaire inutilisé identifié (CommandType utilisé dans CheckoutController). |
| **Français dans le moteur** | ⚪ | Commentaires et messages d’erreur API (ex. ChatApiController : "Le champ \"message\" est requis.") en français – cohérent avec une API consommée par une UI française. Pas de variable/méthode/propriété en français. |
| **Bug de route** | ❌ | Voir § 1.3 – `product_details` au lieu de `app_product_details` dans CartController. |

---

## Synthèse

### Points forts

- Architecture et naming (PSR-4, Symfony, anglais) conformes ; routes et paths en anglais.
- Prod Docker : `volumes: []`, image scellée, pas de dépendance au disque hôte.
- Dockerfile : chaîne Tailwind → AssetMapper → cache prod et permissions www-data claires.
- Entrypoint : attente BDD, migrations, correction des permissions à chaque démarrage.
- Makefile : détection dev/prod, install guidé, protection fixtures en prod, chown après assets en prod.
- .env.example : toutes les variables nécessaires (Symfony, DB, Stripe, Gemini, MongoDB, health check).
- services.yaml : Gemini avec valeur par défaut vide, pas de crash au boot.
- GeminiService : RAG, gestion d’erreurs et messages de fallback sans faire planter le site.

### Points de vulnérabilité

| Priorité | Élément | Détail |
|----------|--------|--------|
| **Haute** | Route CartController | `redirectToRoute('product_details', ...)` invalide → doit être **`app_product_details`**. Sinon redirect après "quantité invalide" ou "stock dépassé" provoque une exception. |
| **Moyenne** | Makefile / Linux | Sur VPS Linux, `open -a Docker` est inutile ; documenter que Docker doit être installé et démarré avant `make install`. |
| **Basse** | Instruction Gemini | Ajouter dans `systemInstruction` une consigne explicite du type : "Réponds uniquement en français, de manière professionnelle et courtoise." |
| **Basse** | Image Docker prod | Optionnel : `composer install --no-dev` et `tailwind:build --minify` dans le Dockerfile pour image plus légère et CSS minifié. |

---

## Liste d’actions pour atteindre la perfection

1. **Corriger le nom de route dans CartController**  
   - Fichier : `src/Controller/Frontend/CartController.php`.  
   - Remplacer `redirectToRoute('product_details', ...)` par `redirectToRoute('app_product_details', ...)` (2 occurrences, lignes 37 et 43).

2. **Documenter la portabilité Linux du Makefile**  
   - Dans le README ou un doc "Déploiement" : préciser que sur Linux/VPS, Docker (et le daemon) doivent être installés et actifs avant d’exécuter `make install` ; les étapes `open -a Docker` et `sleep 30` concernent macOS.

3. **Renforcer l’instruction Gemini (français professionnel)**  
   - Dans `GeminiService::generateResponse()`, compléter `$systemInstruction` par une phrase du type : "Tu réponds uniquement en français, de manière professionnelle et courtoise."

4. **(Optionnel) Optimiser l’image prod**  
   - Dans le Dockerfile : utiliser `composer install --no-dev ...` pour l’étape d’install (ou une étape dédiée prod) et ajouter `--minify` à `tailwind:build` si on souhaite le CSS minifié dans l’image.

---

*Rapport généré en lecture seule. Aucune modification automatique des fichiers.*
