# Merchanto E-Shop

> Implementation plan and decision log.
> Rules and conventions: `.cursor/rules/`
> Deferred improvements: [`TECH_DEBT.md`](TECH_DEBT.md)

## Decisions

| Topic | Choice |
|---|---|
| Commits | Maximal narrow scope, one logical change each |
| Order placement | `PlaceOrderService` (not Action) |
| Category tests | Separate `CategoryManagementTest` |
| Data access | Repository pattern: Controller → Service → Repository → Model |
| Shared repository base | `app/Repositories/` — `RepositoryInterface` + `EloquentRepository` |
| Storefront listing | `ProductListingService` (intra-module, returns Eloquent) |
| Cross-module catalog | `ProductCatalogService` → DTO via `ProductCatalogInterface` (Phase 2) |
| Filament panel registration | `Panel::configureUsing()` in module `register()` |
| Public routes | Module `routes/web.php`, no auth middleware |
| Contract tests | Separate `ProductCatalogContractTest` |
| Add to cart | Phase 4 only (Phase 1 = browse only) |
| README | Written incrementally from Phase 0 |
| Development | Feature-test TDD (Red → Green → Refactor) |

## Layer flow (intra-module)

```
Controller / Livewire / Filament page
        ↓
    Service          ← business logic, orchestration
        ↓
   Repository        ← queries, persistence
        ↓
      Model           ← Eloquent entity only
```

## Cross-module flow

```
Order module
    ↓ ProductCatalogInterface (app/Contracts)
ProductCatalogService (Catalog module)
    ↓ ProductRepositoryInterface
EloquentProductRepository → Product model
```

Order must **not** import `Modules\Catalog\*` models, repositories, or services.

---

## Plan

### Phase 0 — Foundation ✅

- [x] Initialize Laravel project in repo root
- [x] Add Laravel Sail with PostgreSQL
- [x] Configure dev database
- [x] Configure separate test database (`phpunit.xml`)
- [x] Install `nwidart/laravel-modules`
- [x] Install Livewire
- [x] Install Filament
- [x] Install Pest
- [x] Install Laravel Duster
- [x] Install and configure Larastan (level ≥ 5)
- [x] Add GitHub Actions workflow (Pest + Duster + Larastan)
- [x] Create shared public layout (Blade + Vite)
- [x] Set up `User` model + admin seeder
- [x] Configure Filament panel at `/admin`
- [x] Add smoke feature test (app boots, admin can log in)
- [x] Start README: Sail setup, env, run commands

### Phase 1 — Catalog module ✅

- [x] Create `Catalog` module
- [x] Migration: `categories`
- [x] Migration: `products`
- [x] Models: `Category`, `Product` + factories
- [x] **TDD** `CategoryManagementTest` — Filament Category CRUD
- [x] Filament `CategoryResource`
- [x] **TDD** `ProductManagementTest` — Filament Product CRUD
- [x] Filament `ProductResource`
- [x] **TDD** `ProductDisplayTest` — public `/products` list
- [x] Public product browsing page (layout, minimal design)
- [x] Seeders: categories, products
- [x] Shared repo base: `app/Repositories/`
- [x] `ProductRepositoryInterface` + `EloquentProductRepository`
- [x] `ProductListingService` + refactor `ProductController`

### Phase 1.5 — Catalog tech debt (optional, before or during Phase 3)

- [x] `CategoryRepository` + Filament services for Category CRUD
- [x] Refactor `CategoryResource` / `ProductResource` → Service → Repository
- [x] Remove scaffold `CatalogController` and unused module views/routes

### Phase 2 — Contracts ✅

- [x] **TDD** `ProductCatalogContractTest` — resolve contract from container; DTO output, stock decrement
- [x] `ProductCatalogInterface` in `app/Contracts/Catalog/`
- [x] DTOs: readonly `ProductData`, `ProductSnapshot` in `app/DataTransferObjects/Catalog/`
- [x] Extend `ProductRepositoryInterface`: named methods (`findById`, `listAvailable`, `hasStock`, `decrementStock`)
- [x] `ProductCatalogService` implements interface; uses repository only (no direct model access)
- [x] Register `ProductCatalogInterface` → `ProductCatalogService` in `CatalogServiceProvider`

### Phase 3 — Order module (backend)

Build with Repository → Service layers from day one (same pattern as Catalog).

- [ ] Create `Order` module (+ `phpunit.xml` testsuite, Makefile `test-order`)
- [ ] Migration: `orders`
- [ ] Migration: `order_items` (snapshot + `product_id`, no FK to Catalog)
- [ ] `OrderStatus` enum
- [ ] Models: `Order`, `OrderItem` + factories
- [ ] `OrderRepositoryInterface` + `EloquentOrderRepository`
- [ ] `PlaceOrderService` — DB transaction; depends on `ProductCatalogInterface` only (not Catalog module)
- [ ] **TDD** `OrderCreationTest` — place order, items, total, stock decremented via contract
- [ ] `OrderManagementService` for admin status transitions
- [ ] Filament `OrderResource` → `OrderManagementService`
- [ ] **TDD** `OrderManagementTest` — list orders, status transitions in admin
- [ ] Update README: architecture overview (modules, layers, contracts)

### Phase 4 — Cart & checkout (Order module, public frontend)

Livewire and session cart live in **Order module**; badge wired in root layout.

- [ ] `CartService` — session-based cart (no DB table)
- [ ] Livewire: add to cart from `/products`
- [ ] Livewire: cart badge in `resources/views/layouts/app.blade.php` header
- [ ] Public routes in `Modules/Order/routes/web.php`: `/checkout`, `/orders/{order}`
- [ ] `/checkout` — cart review, name + email, place order via `PlaceOrderService`
- [ ] Clear cart after successful order
- [ ] `/orders/{order}` — order view + status display (from snapshot, not Catalog)
- [ ] **TDD** tests: cart, checkout, order view, cross-module snapshot

### Phase 5 — Submission

- [ ] Sample orders seeder (optional; categories/products/admin already seeded)
- [ ] Complete README: install, DB, assets, admin access, demo flow, architecture, test commands
- [ ] Verify setup on a fresh environment
- [ ] All Pest tests green
- [ ] Duster clean
- [ ] Larastan zero errors
- [ ] CI green
- [ ] Tag `v1.0`

---

## Commands

```bash
make up                              # start Sail
make migrate                         # run migrations
make artisan cmd="migrate:fresh --seed"  # reset DB + seed demo data
make assets                          # Filament assets + Vite build
make test                            # all tests
make test-catalog                    # Catalog module tests
make test-order                      # Order module tests
make duster                          # lint
make duster-fix                      # auto-fix style
make stan                            # Larastan (use root-shell if cache errors)
make check                           # test + pint + duster + stan
```
