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

### 1.3 URLs et routes

| Élément | Statut | Détail |
|--------|--------|--------|
| **Paths** | ✅ | `/login`, `/register`, `/products`, `/cart`, `/checkout`, `/reviews`, `/admin/users`, `/api/chat`, `/api/health`, `/webhook/stripe` – anglais. |
| **Noms de routes** | ✅ | `app_login`, `app_register`, `app_product_details`, `cart_view`, `checkout`, `api_chat` – cohérents. |
| **Redirections** | ✅ | CartController utilise bien `app_product_details` pour les redirects (correction appliquée). |

---

## 2. Dockérisation & simulation VPS

### 2.1 Isolement prod

| Élément | Statut | Détail |
|--------|--------|--------|
| **docker-compose.prod.yml** | ✅ | Service `app` : **`volumes: []`** – aucun bind mount, conteneur 100 % basé sur l’image. |
| **env_file** | ✅ | `[.env, .env.local]` – priorité aux fichiers, pas d’override APP_ENV dans environment. |

### 2.2 Dockerfile

| Élément | Statut | Détail |
|--------|--------|--------|
| **Chaîne de build** | ✅ | COPY → rm var/ → mkdir var/cache, var/log → chown www-data + chmod 775 → composer dump-autoload → **tailwind:build** → **asset-map:compile** → **cache:clear --env=prod** → chown var/. |
| **Permissions** | ✅ | `chown -R www-data:www-data /var/www/html` puis `chown -R www-data /var/www/html/var` en fin de build. |
| **Optimisation** | ⚪ | Optionnel : `composer install --no-dev` et `tailwind:build --minify` pour image prod plus légère. |

### 2.3 Entrypoint

| Élément | Statut | Détail |
|--------|--------|--------|
| **Attente BDD** | ✅ | Boucle `doctrine:query:sql "SELECT 1"` jusqu’à 60 tentatives. |
| **Migrations** | ✅ | `doctrine:migrations:migrate --no-interaction` après attente. |
| **Permissions** | ✅ | `chown -R www-data:www-data /var/www/html/var /var/www/html/public` avant Apache. |
| **Composer si besoin** | ✅ | Si `vendor/autoload.php` absent → `composer install`. |

---

## 3. Configuration & automatisation

### 3.1 Makefile ("Tour de Contrôle")

| Élément | Statut | Détail |
|--------|--------|--------|
| **Détection env** | ✅ | APP_ENV depuis .env.local puis .env, défaut dev ; COMPOSE_FILE et COMPOSE dérivés. |
| **install** | ✅ | Vérif .env / .env.local, open Docker + sleep 30, up --build, composer install, assets (prod: minify + asset-map), chown en prod, migrate, fixtures (bloquées en prod). |
| **Portabilité Mac vierge** | ✅ | `make install` suffit après création/remplissage de .env.local. |
| **Portabilité Linux / VPS** | ⚠️ | `open -a Docker` et `sleep 30` sont orientés macOS. Sur Linux, Docker doit être installé et le daemon démarré avant `make install`. |

### 3.2 .env.example

| Élément | Statut | Détail |
|--------|--------|--------|
| **Variables** | ✅ | APP_ENV, APP_SECRET, APP_DEBUG, TRUSTED_PROXIES, DATABASE_URL, DB_*, MAILER_DSN, STRIPE_*, MONGODB_*, GOOGLE_GEMINI_API_KEY, HEALTH_CHECK_TOKEN. |
| **Commentaires** | ✅ | En anglais, description en tête. |

### 3.3 services.yaml

| Élément | Statut | Détail |
|--------|--------|--------|
| **Gemini** | ✅ | `default_gemini_api_key: ''` et `env(default:default_gemini_api_key:GOOGLE_GEMINI_API_KEY)` – pas de crash si variable absente. |
| **Injections** | ✅ | AiServiceInterface → GeminiService ; ReviewRepository en factory ODM. |

---

## 4. Intégration IA (Gemini)

| Élément | Statut | Détail |
|--------|--------|--------|
| **RAG** | ✅ | `buildRagContext()` : produits (nom, prix, description) + avis (note, commentaire) dans le prompt. |
| **Gestion d’erreurs** | ✅ | Clé vide, TransportException, HTTP ≠ 200, payload inattendu, Throwable → log + message de fallback, pas de crash. |
| **Instruction langue** | ✅ | `systemInstruction` inclut : "Tu dois répondre exclusivement en français, de manière professionnelle, courte et courtoise." |

---

## 5. Cohérence globale

| Élément | Statut | Détail |
|--------|--------|--------|
| **Fichiers orphelins** | ✅ | Aucun identifié (CommandType utilisé dans CheckoutController). |
| **Français moteur** | ✅ | Naming technique en anglais ; commentaires et messages utilisateur en français (conforme). |

---

## Synthèse

### Points forts

- **Naming & architecture** : PSR-4, namespaces, entités, services, repositories en anglais ; routes et paths cohérents ; redirections CartController corrigées (`app_product_details`).
- **Docker prod** : `volumes: []` → image scellée, pas de dépendance au disque hôte.
- **Dockerfile** : chaîne Tailwind → AssetMapper → cache prod, permissions www-data ; entrypoint : attente BDD, migrations, chown à chaque démarrage.
- **Makefile** : détection dev/prod, `make install` opérationnel sur Mac vierge, protection fixtures en prod, chown après assets en prod.
- **.env.example** : toutes les variables nécessaires (Symfony, DB, Stripe, Gemini, MongoDB, health check).
- **services.yaml** : Gemini avec valeur par défaut vide ; injections robustes.
- **GeminiService** : RAG, gestion d’erreurs complète, fallback messages ; instruction explicite de réponse en français professionnel.

### Points de vulnérabilité restants

| Priorité | Élément | Détail |
|----------|--------|--------|
| **Moyenne** | Makefile / Linux | Sur VPS Linux, `open -a Docker` est sans effet. Documenter que Docker doit être installé et le daemon démarré avant `make install`. |
| **Basse** | Image Docker prod | Optionnel : `composer install --no-dev` et `tailwind:build --minify` dans le Dockerfile. |

---

## Liste d’actions pour atteindre la perfection

1. **Documenter la portabilité Linux du Makefile**  
   Dans le README ou une doc "Déploiement" : préciser que sur Linux/VPS, Docker (et le daemon) doivent être installés et actifs avant d’exécuter `make install`.

2. **(Optionnel) Optimiser l’image prod**  
   Dans le Dockerfile : `composer install --no-dev` et `tailwind:build --minify` pour une image plus légère et un CSS minifié.

---

*Rapport généré en lecture seule. Corrections déjà appliquées : CartController (app_product_details), GeminiService (instruction français).*
