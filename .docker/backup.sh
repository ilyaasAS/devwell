#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="${SCRIPT_DIR%/.docker}"
BACKUP_ROOT="${PROJECT_ROOT}/backups"
TIMESTAMP="$(date +'%Y%m%d-%H%M%S')"
BACKUP_DIR="${BACKUP_ROOT}/${TIMESTAMP}"

mkdir -p "${BACKUP_DIR}"

# Safe defaults for local/prod
DB_USER="${DB_USER:-app}"
DB_PASSWORD="${DB_PASSWORD:-!ChangeMe!}"
DB_NAME="${DB_NAME:-devwell}"
MONGODB_URL="${MONGODB_URL:-mongodb://mongodb:27017}"
MONGODB_DB="${MONGODB_DB:-devwell}"

echo "Creating backup directory: ${BACKUP_DIR}"

echo "Backing up MariaDB database '${DB_NAME}'..."
docker compose -f "${PROJECT_ROOT}/docker-compose.prod.yml" exec -T app \
  mysqldump --skip-ssl -h database -u"${DB_USER}" -p"${DB_PASSWORD}" "${DB_NAME}" \
  > "${BACKUP_DIR}/mariadb.sql"
echo "MariaDB backup completed: ${BACKUP_DIR}/mariadb.sql"

echo "Backing up MongoDB database '${MONGODB_DB}'..."
docker compose -f "${PROJECT_ROOT}/docker-compose.prod.yml" exec -T app \
  mongodump --uri="${MONGODB_URL}" --db="${MONGODB_DB}" --archive \
  > "${BACKUP_DIR}/mongodb.archive"
echo "MongoDB backup completed: ${BACKUP_DIR}/mongodb.archive"

echo "All backups completed successfully."

