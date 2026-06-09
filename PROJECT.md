# Merchanto E-Shop

> Implementation plan and decision log.
> Rules and conventions: `.cursor/rules/`

## Decisions

| Topic | Choice |
|---|---|
| Commits | Maximal narrow scope, one logical change each |
| Order placement | `PlaceOrderService` (not Action) |
| Category tests | Separate `CategoryManagementTest` |
| Data access | Repository pattern: Controller → Service → Repository → Model |
| Contract tests | Separate `ProductCatalogContractTest` |
| Add to cart | Phase 4 only (Phase 1 = browse only) |
| README | Written incrementally from Phase 0 |
| Development | Feature-test TDD (Red → Green → Refactor) |

## Plan

### Phase 0 — Foundation

- [ ] Initialize Laravel project in repo root
- [ ] Add Laravel Sail with PostgreSQL
- [ ] Configure dev database
- [ ] Configure separate test database (`.env.testing`, `phpunit.xml`)
- [ ] Install `nwidart/laravel-modules`
- [ ] Install Livewire
- [ ] Install Filament
- [ ] Install Pest
- [ ] Install Laravel Duster
- [ ] Install and configure Larastan (level ≥ 5)
- [ ] Add GitHub Actions workflow (Pest + Duster + Larastan)
- [ ] Create shared public layout (Blade + Vite)
- [ ] Set up `User` model + admin seeder
- [ ] Configure Filament panel at `/admin`
- [ ] Add smoke feature test (app boots, admin can log in)
- [ ] Start README: Sail setup, env, run commands

### Phase 1 — Catalog module

- [ ] Create `Catalog` module
- [ ] Migration: `categories`
- [ ] Migration: `products`
- [ ] Models: `Category`, `Product` + factories
- [ ] **TDD** `CategoryManagementTest` — Filament Category CRUD
- [ ] Filament `CategoryResource`
- [ ] **TDD** `ProductManagementTest` — Filament Product CRUD
- [ ] Filament `ProductResource`
- [ ] **TDD** `ProductDisplayTest` — public `/products` list
- [ ] Public product browsing page (layout, minimal design)
- [ ] Seeders: categories, products

### Phase 2 — Contracts

- [ ] `ProductCatalogInterface` in `app/Contracts/Catalog/`
- [ ] DTOs: `ProductData`, `ProductSnapshot` in `app/DataTransferObjects/Catalog/`
- [ ] `ProductCatalogService` in Catalog module
- [ ] Register binding in `CatalogServiceProvider`
- [ ] Contract methods: list, find, check stock, decrement stock
- [ ] **TDD** `ProductCatalogContractTest` — DTO output, stock decrement

### Phase 3 — Order module (backend)

- [ ] Create `Order` module
- [ ] Migration: `orders`
- [ ] Migration: `order_items` (snapshot + `product_id`, no FK)
- [ ] `OrderStatus` enum
- [ ] Models: `Order`, `OrderItem` + factories
- [ ] `PlaceOrderService` (DB transaction + contract + snapshot)
- [ ] **TDD** `OrderCreationTest` — place order, items, total, stock decremented
- [ ] Filament `OrderResource`
- [ ] **TDD** `OrderManagementTest` — list orders, status transitions in admin
- [ ] Update README: architecture overview (modules, contracts)

### Phase 4 — Cart & checkout (public frontend)

- [ ] Session cart (no DB table)
- [ ] Livewire: add to cart from `/products`
- [ ] Livewire: cart badge in layout header (top-right)
- [ ] `/checkout` — cart review, name + email, place order via `PlaceOrderService`
- [ ] Clear cart after successful order
- [ ] `/orders/{order}` — order view + status display
- [ ] **TDD** extend/add tests: cart, checkout, order view, cross-module snapshot

### Phase 5 — Submission

- [ ] Demo seeders (categories, products, admin; sample orders optional)
- [ ] Complete README: install, DB, assets, admin access, demo flow, test commands (all + per module)
- [ ] Verify setup on a fresh environment
- [ ] All Pest tests green
- [ ] Duster clean
- [ ] Larastan zero errors
- [ ] CI green
- [ ] Tag `v1.0`

## Commands

_TBD — fill in during Phase 0_
