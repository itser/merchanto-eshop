# Technical Debt

> Deferred improvements tracked outside the main delivery plan (`PROJECT.md`).
> Rules and conventions: `.cursor/rules/`

## Index

| ID | Topic | Priority | Target phase |
|---|---|---|---|
| TD-001 | Money value object | Medium | After Phase 3 (or when multi-currency is required) |

---

## TD-001 — Money value object

### Current state

- `products.price` — `decimal(10, 2)` in PostgreSQL, no `currency` column.
- Eloquent cast: `'price' => 'decimal:2'` (returns `string`).
- Cross-module DTOs use `string $price`:
  - `App\DataTransferObjects\Catalog\ProductData`
  - `App\DataTransferObjects\Catalog\ProductSnapshot`
- Order snapshots (Phase 3) will store a plain decimal string — single implicit currency.

This is intentional for Phase 2–4: one currency, minimal contract surface, no float rounding issues.

### Problem / why change later

- `string $price` carries no currency — unsafe once products or orders can use multiple ISO-4217 codes.
- Arithmetic (line totals, order total, tax) requires manual `bcmath` discipline.
- Snapshot fields should freeze `(amount, currency)` at order time, not amount alone.

### Recommended approach

Use **[brick/money](https://github.com/brick/money)** — do not build a custom Money VO from scratch when currencies and conversion are on the roadmap.

Place shared types in `app/` (cross-module, same as DTOs):

```
app/ValueObjects/Money.php          ← thin wrapper or alias around brick/money (optional)
app/DataTransferObjects/Catalog/    ← ProductData, ProductSnapshot use Money
```

### Scope of work

1. **Dependency**
   - `composer require brick/money`

2. **Database**
   - Add `currency CHAR(3) NOT NULL DEFAULT 'EUR'` (or project default) to:
     - `products`
     - `orders` (order-level currency)
     - `order_items` (snapshot currency alongside price)
   - Keep `price` as `decimal(10, 2)` — store major units or migrate to minor units only if brick/money storage strategy requires it (document choice in migration).

3. **Eloquent**
   - Custom cast `MoneyCast` (or use accessor/mutator) on `Product`, `Order`, `OrderItem`.
   - Factory/seeder defaults include currency.

4. **DTOs & contract**
   - Replace `string $price` with `Money $price` in `ProductData` and `ProductSnapshot`.
   - Update `ProductCatalogContractTest` assertions (e.g. `->amount()` / `->getCurrency()->getCurrencyCode()`).
   - `ProductCatalogService` maps repository entities → DTOs with Money.

5. **Order module**
   - `PlaceOrderService` totals via `Money::plus()` / `Money::multipliedBy()` — never raw floats.
   - Snapshot writes copy `Money` from catalog DTO at order time.

6. **Presentation**
   - Blade/Livewire: format via `Money` formatter or a single `formatMoney(Money $money): string` helper.
   - Filament columns: custom format state using Money.

7. **Static analysis**
   - PHPStan generics/annotations for cast return types.
   - Larastan level ≥ 5 clean after changes.

### Out of scope (unless explicitly required)

- FX rate tables and runtime currency conversion
- Tax/VAT engines
- Payment gateway amount handling

Track those as separate debt items if needed.

### Acceptance criteria

- [ ] No `float` used for money anywhere in application code
- [ ] DTOs and order snapshots include currency
- [ ] Adding USD product + EUR product cannot produce a valid single-currency total without explicit conversion
- [ ] Existing Pest suite updated; contract and order tests green
- [ ] `make check` passes

### Notes

- Defer until Phase 3 order snapshots exist — then the migration path is clear.
- If the project stays single-currency permanently, `string $price` remains acceptable; close TD-001 as wontfix.
