# Technical Debt

> Deferred improvements — **not** the main plan (`PROJECT.md`).
> Rules: `.cursor/rules/`

## What counts as tech debt here

**In:** things that work today but we deliberately postpone — refactors, CI polish, missing docs.

**Out:** planned features (Order module, cart, README) and routine Phase 3 setup steps — those live in `PROJECT.md`.

**Rule of thumb:** if v1 can ship without it, but we'd want it before scaling or live review → tech debt.

---

## Index

| ID | Topic | When |
|---|---|---|
| TD-001 | Money value object (`brick/money`) | After Phase 3 or if multi-currency |
| TD-002 | Split CI into separate GitHub Actions jobs | After Phase 3 / submission polish |
| TD-003 | `docs/PATTERNS.md` for live review | Done (see `docs/PATTERNS.md`) |
| TD-004 | Constructor injection in HTTP controllers | Phase 5 / submission polish |
| TD-005 | Additional `OrderPlaced` listeners (email, queue) | Phase 5 / submission polish |
| TD-006 | Audit domain events across modules | Phase 5 / submission polish |

---

## TD-001 — Money value object

**Now:** `string $price`, no `currency` column — fine for single-currency v1.

**Later:** `brick/money`, `currency` on products/orders/order_items, DTOs + snapshots use `Money`, totals via safe arithmetic.

**Close as wontfix** if project stays single-currency forever.

---

## TD-002 — Split GitHub Actions jobs

**Now:** one job `quality` — pest → duster → phpstan. Meets assignment bonus.

**Later:** separate jobs `tests` / `lint` / `static-analysis` (parallel where possible); shared composer cache; PostgreSQL only on `tests`. Optional per-module jobs when suite grows.

Not required by the assignment — DX improvement only.

---

## TD-003 — Patterns doc

**Done:** [`docs/PATTERNS.md`](PATTERNS.md) — Modular Monolith, Repository, Ports & Adapters, DTO, Snapshot, domain events, order status workflow. Linked from README.

---

## TD-004 — Constructor injection in HTTP controllers

**Now:** services are resolved via **method injection** in controller actions (e.g. `CheckoutService $checkout` in `index()` and `store()`). Matches current thin-controller style (`ProductController`, `CheckoutController`).

**Later (Phase 5):** refactor controllers that share a dependency across actions to **constructor injection** (`private readonly CheckoutService $checkout`), so deps are declared once per controller. Update `.cursor/rules/` if we standardize on constructor over method injection.

Not a functional issue — consistency / readability only.

---

## TD-005 — Additional domain event listeners

**Now:** `OrderPlaced` → `ClearCartOnOrderPlaced` (sync). Cart clear removed from `CheckoutService::place()`.

**Later (Phase 5):**

- `SendOrderConfirmation` listener (email to customer)
- Admin notification on new order
- Queue listeners where async is safe (`ShouldQueue`)
- `OrderStatusChanged` event when admin updates status in Filament

See `.cursor/rules/events.mdc` for dispatch and listener conventions.

---

## TD-006 — Audit domain events across modules

**Now:** only `OrderPlaced` → `ClearCartOnOrderPlaced` is implemented. Other actions have no domain events (by design for v1).

**Later (Phase 5):** review services and use cases for side effects that deserve events + listeners. Candidates to evaluate (add only if a concrete listener exists):

| Area | Service / action | Possible event | Possible listener |
|---|---|---|---|
| Order | `OrderManagementService::update` (status) | `OrderStatusChanged` | customer email, admin log |
| Order | `PlaceOrderService::place` | (already `OrderPlaced`) | `SendOrderConfirmation` (TD-005) |
| Catalog | `ProductManagementService::create/update/delete` | `ProductCreated`, etc. | only if cache/search/webhook needed |
| Catalog | `CategoryManagementService::*` | unlikely for v1 | — |
| Order | `AddToCartButton` / `CartService` | no Laravel event | Livewire `cart-updated` is enough |

**Rule:** do not add events for CRUD without a listener; see `.cursor/rules/events.mdc`.

**Outcome:** short list in README architecture or `docs/PATTERNS.md` (TD-003) of which events exist and why.

