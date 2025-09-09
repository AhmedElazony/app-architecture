# Laravel Application Architecture

This repository is a lightweight starter template that defines the base application architecture I use for new Laravel projects. It includes a recommended directory layout, example configuration, Docker support, and a minimal set of helpers to get started quickly.

## Quick checklist

- Requirements: PHP 8.1+, Composer, Docker & Docker Compose (optional but recommended)

## Installation (Docker + Makefile)

This template includes a `Makefile` with convenient targets to prepare the project. From the repository root use the following workflow:

```bash
# build docker images (optional: useful when you changed Dockerfile)
make images

# start services in background
docker compose up -d

# generate local self-signed TLS certs (optional)
make certs

# install project dependencies and prepare .env
make install

# run database migrations and seeders
docker compose exec app php artisan migrate --seed --force
```

Notes:
- `make install` runs `composer install`, copies `.env.example` to `.env`, and generates the app key inside the container (it already takes care of common PHP setup).
- If your environment uses the legacy `docker-compose` command, replace `docker compose` with `docker-compose` above.
- Use `make bash` to open a shell in the `app` container, and `make fix-permissions` if you need to fix storage/cache permissions.

## Project structure (high level)

- `app/` - Domain and HTTP layers organized by feature. Example: `Domains/Auth/Models/User.php`.
- `app/Domains/` - Domain layer that have all the business entities separated by it Domain (Bounded Context). Each domain has its own directories and files such as (Enums, Actions, Services, Models, Events, etc.)
- `docker/` - Dockerfile and service config for development.
- `.github/workflows` - CI/CD pipeline files using github actions.

This template intentionally uses a domain-oriented layout under `app/Domains` to encourage feature grouping and separation.

## Troubleshooting

- Permission issues: If you see storage or bootstrap cache permission errors, ensure `storage/` and `bootstrap/cache` are writable by the webserver or adjust permissions using the included helper scripts in `docker/php/fix-permissions.sh`.