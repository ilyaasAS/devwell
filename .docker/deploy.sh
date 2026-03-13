#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="${SCRIPT_DIR%/.docker}"

cd "$PROJECT_ROOT"

git pull --rebase

docker compose -f docker-compose.prod.yml up -d --build

docker compose -f docker-compose.prod.yml exec app php bin/console cache:clear --env=prod

