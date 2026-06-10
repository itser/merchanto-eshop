# Architecture Patterns

Short reference for live review. Conventions and rules: `.cursor/rules/`. Implementation plan: `PROJECT.md`.

## Modular Monolith

Two bounded contexts via `nwidart/laravel-modules`:

| Module | Responsibility |
|--------|----------------|
| **Catalog** | Products, categories, Filament CRUD, public product listing |
| **Order** | Cart, checkout, orders, Filament order admin |

Modules do **not** import each other's models, repositories, or services. Shared cross-module contracts live in `app/`.

```
Modules/Catalog          Modules/Order
     │                         │
     │   ProductCatalogInterface (app/Contracts)
     └───────────┬─────────────┘
                 ↓
         DTOs & exceptions in app/
```

## Repository Pattern

Intra-module data access follows a strict layer flow:

```
Controller / Livewire / Filament page
        ↓
    Service          ← business logic, orchestration
        ↓
   Repository        ← queries, persistence
        ↓
      Model           ← Eloquent entity only
```

Shared base: `app/Repositories/Contracts/RepositoryInterface` and `app/Repositories/Eloquent/EloquentRepository`.

Examples: `ProductRepositoryInterface` → `EloquentProductRepository`, `OrderRepositoryInterface` → `EloquentOrderRepository`.

## Ports & Adapters

**Port:** `App\Contracts\Catalog\ProductCatalogInterface` — what Order needs from Catalog (find product, check stock, decrement stock).

**Adapter:** `Modules\Catalog\Services\ProductCatalogService` — implements the port using `ProductRepositoryInterface` only.

Order services (`PlaceOrderService`, `CheckoutService`, `AddToCartButton`) depend on the **interface**, never on `Modules\Catalog\*`.

Binding: `CatalogServiceProvider` → `ProductCatalogInterface` → `ProductCatalogService`.

## DTO & Snapshot

**DTO (read, cross-module):** `App\DataTransferObjects\Catalog\ProductData` — readonly product data returned by the catalog port.

**Snapshot (write, order time):** order line items store `product_name`, `product_price`, `quantity` plus `product_id` (reference only, **no FK** to Catalog). Price and name are frozen at checkout; public order view reads the snapshot, not live catalog data.

## Domain Events

Side effects after a successful business action — not core persistence inside the same use case.

| Event | Dispatched from | Listener | Purpose |
|-------|-----------------|---------|---------|
| `OrderPlaced` | `PlaceOrderService` (after DB commit) | `ClearCartOnOrderPlaced` | Empty session cart |
| `OrderStatusChanged` | `OrderManagementService` (after status update) | `LogOrderStatusChanged` | Placeholder for email / audit (TD-005) |

Dispatch **after** `DB::transaction()` completes. See `.cursor/rules/events.mdc`.

## Order Status Workflow

Linear workflow (v1): `pending → confirmed → shipped → delivered`.

Rules live on `Modules\Order\Enums\OrderStatus`:

- `canTransitionTo()` — domain validation (no skip, no backward, `delivered` is terminal)
- `allowedTransitions()` — Filament status select options

`OrderManagementService` throws `InvalidOrderStatusTransitionException` when a transition is invalid. Filament also restricts the select to allowed values (form validation before the service).

**Evolution:** if the workflow branches (cancel, refund, per-order-type rules), extract a **State** pattern from the enum. Current enum methods are a lightweight State for a linear graph.

## Domain Exceptions

Functional failures use dedicated exception classes — not generic `\Exception`.

Cross-module: `app/Exceptions/Catalog/` (e.g. `InsufficientStockException`).

Module-internal: `Modules/Order/app/Exceptions/` (e.g. `InvalidOrderStatusTransitionException`).

## HTTP Input

POST body validation uses Form Request classes (`CheckoutRequest`), not inline `$request->validate()` in controllers. Livewire and Filament keep validation in components / resource forms.

## Deferred Patterns

See `TECH_DEBT.md`: Money value object (`brick/money`), additional domain event listeners, full State classes for order status.
