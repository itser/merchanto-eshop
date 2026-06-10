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
| TD-003 | `docs/PATTERNS.md` for live review | Before README / review |
| TD-004 | Constructor injection in HTTP controllers | Phase 5 / submission polish |
| TD-005 | Additional `OrderPlaced` listeners (email, queue) | Phase 5 / submission polish |

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

**Now:** patterns live only in `.cursor/rules/`.

**Later:** short `docs/PATTERNS.md` — Modular Monolith, Repository, Ports & Adapters (`ProductCatalogInterface`), DTO, State (`OrderStatus`). Link from README.

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
