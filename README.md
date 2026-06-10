# Merchanto E-Shop

Modular Laravel 12 e-shop: Livewire storefront, Filament admin, PostgreSQL, Pest feature tests.

**Stack:** Laravel Sail · `nwidart/laravel-modules` (Catalog, Order) · Livewire · Filament · Vite · Duster · Larastan · GitHub Actions

## Setup

```bash
cp .env.example .env
composer install
make up
make artisan cmd="key:generate"
make artisan cmd="migrate:fresh --seed"
make npm cmd="install"
make assets
```

| URL | Purpose |
|-----|---------|
| http://localhost:8080 | Storefront |
| http://localhost:8080/admin | Admin (Filament) |

**Admin:** `admin@merchanto-eshop.test` / `password` (from `.env.example`)

Use `http://localhost:8080` only — not `127.0.0.1` (different session cookies).

### Environment

| Variable | Value | Note |
|----------|-------|------|
| `APP_URL` | `http://localhost:8080` | Must match browser URL |
| `DB_HOST` | `pgsql` | Sail service name |
| `SESSION_DRIVER` | `database` | Required for cart & admin |

Test DB: `merchanto_eshop_testing` (see `phpunit.xml`) — isolated from dev data.

### Assets

`make assets` = Filament publish + Vite build → `public/build/`. Re-run after frontend or Filament changes. Filament assets also publish on `composer install`.

## Fresh install verification

Run on a **clean clone** (or after `make down -v` + remove `vendor/`, `node_modules/`):

```bash
# 1. Setup (must finish without errors)
cp .env.example .env && composer install && make up
make artisan cmd="key:generate"
make artisan cmd="migrate:fresh --seed"
make npm cmd="install" && make assets

# 2. Automated checks
make test
make duster
make stan    # if cache lock error — see Tests & quality section
```

**Browser smoke test** (http://localhost:8080):

- [ ] `/products` — list loads, **Add to cart** visible on in-stock items
- [ ] Cart badge in header updates after add
- [ ] `/checkout` — empty cart redirects to products; with items — form works
- [ ] Place order → `/orders/{id}` shows snapshot data & status
- [ ] `/admin` — login works; products, categories, **3 sample orders** visible

If all pass, README setup is verified.

## Demo flow (evaluators)

After `migrate:fresh --seed` you get categories, products, admin user, and **3 sample orders**.

1. **Browse** — http://localhost:8080/products  
2. **Add to cart** — Livewire button; cart badge updates in header  
3. **Checkout** — http://localhost:8080/checkout — name + email, place order  
4. **Order view** — redirect to `/orders/{id}`; items shown from **snapshot** (not live catalog)  
5. **Admin** — http://localhost:8080/admin — CRUD products/categories, list orders, change status: `pending → confirmed → shipped → delivered`

**No public customer auth** — guest checkout only. Order list for customers is not in scope; admin sees all orders in Filament.

## Storefront routes

| Route | Description |
|-------|-------------|
| `/products` | Product catalog |
| `/checkout` | Cart review + customer form |
| `/orders/{order}` | Order confirmation & status |

Cart is **session-only** (no DB table). Order is persisted on checkout submit; cart clears via `OrderPlaced` event.

## Tests & quality

```bash
make test              # all Pest tests (58+)
make test-catalog      # Catalog module
make test-order        # Order module
make duster            # code style
make stan              # Larastan (level ≥ 5)
make check             # test + duster + stan
```

If `make stan` fails on cache lock:  
`./vendor/bin/sail exec laravel.test sh -c 'rm -rf /tmp/phpstan && php vendor/bin/phpstan analyse'`

## Architecture

```
Modules/Catalog          Modules/Order
     │                         │
     │   ProductCatalogInterface (app/Contracts)
     └───────────┬─────────────┘
                 ↓
         DTOs & exceptions in app/
```

**Layers (each module):** Controller / Livewire / Filament → Service → Repository → Model

**Cross-module rule:** Order never imports `Modules\Catalog\*`. Catalog data at checkout/order time goes through `ProductCatalogInterface`; order line items store a **snapshot** (`product_name`, `product_price`, `quantity`) + `product_id` (no FK).

**Patterns:** Form Requests for HTTP input · domain events for side effects (`OrderPlaced` → clear cart) · module routes in `Modules/*/routes/web.php`

Conventions: `.cursor/rules/` · plan: `PROJECT.md` · deferred work: `TECH_DEBT.md`

## CI

GitHub Actions on push/PR to `main`: Pest → Duster → Larastan (`.github/workflows/ci.yml`).
