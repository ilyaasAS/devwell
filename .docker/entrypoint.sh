#!/usr/bin/env bash
set -e

# Si le code est monté sans vendor/ (hôte sans PHP), installer les dépendances
if [ ! -f vendor/autoload.php ]; then
  echo "[entrypoint] vendor/ absent, exécution de composer install..."
  composer install --no-interaction --prefer-dist
fi

echo "[entrypoint] En attente de la base de données MariaDB..."

max_attempts=60
attempt=0

until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  attempt=$((attempt + 1))
  if [ $attempt -ge $max_attempts ]; then
    echo "[entrypoint] Timeout: MariaDB non prête." >&2
    exit 1
  fi
  sleep 1
done

echo "[entrypoint] Base de données prête. Lancement des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "[entrypoint] Ajustement des permissions (var/ et public/) pour www-data..."
chown -R www-data:www-data /var/www/html/var /var/www/html/public 2>/dev/null || true

echo "[entrypoint] Démarrage d'Apache."
exec apache2-foreground