# Merchanto E-Shop

Laravel 12 e-commerce application (technical assignment). Modular architecture with Livewire storefront and Filament admin panel.

## Requirements

- Docker Desktop (or compatible runtime)
- Composer (for initial `vendor/` install outside Sail, optional)

## Quick start

```bash
cp .env.example .env
composer install          # publishes Filament JS/fonts via composer hook
make up
make artisan cmd="key:generate"
make migrate
make artisan cmd="db:seed"
make npm cmd="install"
make assets               # filament:assets + Vite build (admin theme)
```

Open:

- Storefront: http://localhost:8080
- Admin: http://localhost:8080/admin

Default admin credentials (see `.env.example`):

| Field    | Value                      |
|----------|----------------------------|
| Email    | `admin@merchanto-eshop.test` |
| Password | `password`                 |

Use `http://localhost:8080` consistently — do not mix with `127.0.0.1` (separate cookie origin).

## Environment

Key variables in `.env.example`:

| Variable         | Local value              | Notes                                      |
|------------------|--------------------------|--------------------------------------------|
| `APP_URL`        | `http://localhost:8080`  | Must match the URL you open in the browser |
| `APP_PORT`       | `8080`                   | Sail HTTP port                             |
| `DB_HOST`        | `pgsql`                  | Sail service name                          |
| `SESSION_DRIVER` | `database`               | Required for browser login (not `array`)   |

## Make commands

```bash
make help          # list all commands
make up / down     # start / stop Sail
make migrate       # run migrations
make assets        # publish Filament assets + build Vite bundle
make npm-dev       # Vite dev server (hot reload)
make test          # Pest feature tests
make duster        # code style lint
make stan          # Larastan static analysis
make check         # test + pint + duster + stan
```

## Frontend assets

Two separate asset pipelines:

1. **Filament JS/fonts** — published to `public/js/filament/`, `public/fonts/filament/` via `php artisan filament:assets`. Runs automatically on `composer install` / `composer update` (composer hook). Not committed to git.
2. **Vite bundle** (app CSS/JS + Filament theme) — built to `public/build/` via `npm run build` or `make build`. Not committed to git.

After cloning or updating Filament, run:

```bash
make assets
```

## Tests

Tests use a separate PostgreSQL database (`merchanto_eshop_testing`), configured in `phpunit.xml`.

```bash
make test
```

## CI

GitHub Actions runs Pest, Duster, and Larastan on push/PR to `main`. See `.github/workflows/ci.yml`.
