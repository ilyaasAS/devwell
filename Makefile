SHELL := /bin/bash

COMPOSE ?= docker compose
APP_SERVICE ?= app

.DEFAULT_GOAL := help

.PHONY: help install migrate up stop restart logs fixtures fix-mapping backup

help: ## Affiche cette aide
	@echo ""
	@echo "===== Devwell – Commandes Make ====="
	@echo ""
	@grep -E '^[a-zA-Z0-9_-]+:.*?## ' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

install: ## Installation complète Devwell (env, Docker, assets, fixtures)
	@if [ ! -f .env ]; then \
	  echo "Fichier .env introuvable. Merci de le créer avant de lancer 'make install'."; \
	  exit 1; \
	fi
	@if [ ! -f .env.local ]; then \
	  cp .env .env.local; \
	  echo ""; \
	  echo ">> .env.local vient d'être créé à partir de .env."; \
	  echo ">> Merci de remplir manuellement tes clés sensibles (Stripe, Gemini, etc.) dans .env.local,"; \
	  echo ">> puis relance 'make install'."; \
	  echo ""; \
	  exit 1; \
	fi
	@echo ">> Lancement de Docker Desktop (si nécessaire)..."
	@open -a Docker >/dev/null 2>&1 || true
	@echo ">> Attente de Docker (30s)..."
	@sleep 30
	@echo ">> Démarrage de la stack Docker (build inclus)..."
	@$(COMPOSE) up -d --build
	@echo ">> Installation des dépendances PHP (Composer)..."
	@$(COMPOSE) exec $(APP_SERVICE) composer install --no-interaction --optimize-autoloader
	@echo ">> Lecture de l'environnement (Priorité au .env.local)..."
	@APP_ENV=$$(grep -E '^APP_ENV=' .env.local 2>/dev/null | cut -d'=' -f2 | tr -d '\r'); \
	if [ -z "$$APP_ENV" ]; then APP_ENV=$$(grep -E '^APP_ENV=' .env 2>/dev/null | cut -d'=' -f2 | tr -d '\r'); fi; \
	if [ -z "$$APP_ENV" ]; then APP_ENV=dev; fi; \
	if [ "$$APP_ENV" = "prod" ]; then \
		echo ">> Mode PROD détecté : Minification et figeage des assets..."; \
		$(COMPOSE) exec $(APP_SERVICE) php bin/console tailwind:build --minify; \
		$(COMPOSE) exec $(APP_SERVICE) php bin/console asset-map:compile; \
	else \
		echo ">> Mode DEV détecté : Build Tailwind classique..."; \
		$(COMPOSE) exec $(APP_SERVICE) php bin/console tailwind:build; \
	fi
	@echo ">> Vérification et création des tables (Migrations)..."
	@$(MAKE) migrate
	@echo ">> Chargement des fixtures (protégé contre APP_ENV=prod)..."
	@$(MAKE) fixtures

migrate: ## Exécute manuellement les migrations Doctrine
	@echo ">> Exécution des migrations Doctrine dans le conteneur $(APP_SERVICE)..."
	@$(COMPOSE) exec $(APP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction

fixtures: ## Charge les fixtures (interdit en APP_ENV=prod)
	@echo ">> Vérification de APP_ENV dans le conteneur $(APP_SERVICE)..."
	@$(COMPOSE) exec $(APP_SERVICE) sh -lc '\
	  if [ "$$APP_ENV" = "prod" ]; then \
	    echo ""; \
	    echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"; \
	    echo "  Refus d exécuter doctrine:fixtures:load en APP_ENV=prod."; \
	    echo "  Cette commande purgerait la base de données de production."; \
	    echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"; \
	    echo ""; \
	    exit 1; \
	  fi; \
	  php bin/console doctrine:fixtures:load --no-interaction \
	'

up: ## Démarre la stack Docker (build si nécessaire)
	@echo ">> docker compose up -d --build"
	@$(COMPOSE) up -d --build

stop: ## Stoppe les conteneurs sans supprimer les volumes
	@echo ">> docker compose stop"
	@$(COMPOSE) stop

restart: ## Redémarre les conteneurs
	@echo ">> docker compose restart"
	@$(COMPOSE) restart

logs: ## Affiche les logs de l'application (suivi en temps réel)
	@echo ">> docker compose logs -f $(APP_SERVICE)"
	@$(COMPOSE) logs -f $(APP_SERVICE)

fix-mapping: ## Lance make:entity User pour corriger le mapping Order/User
	@echo ">> Lancement de make:entity User dans le conteneur $(APP_SERVICE)..."
	@$(COMPOSE) exec $(APP_SERVICE) php bin/console make:entity User

backup: ## Sauvegarde la base de données MariaDB (Capital & Terres)
	@echo ">> Création de la sauvegarde MariaDB..."
	@mkdir -p backups
	@$(COMPOSE) exec database mysqldump -u root --password=root devwell > backups/backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo ">> Sauvegarde terminée dans le dossier backups/"