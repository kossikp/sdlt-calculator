# SDLT Calculator (Laravel)

Standalone Stamp Duty Land Tax (SDLT) calculator for residential purchases in England and Northern Ireland.

## What this app does

- Calculates SDLT for:
  - standard residential rates
  - first-time buyer relief
  - additional property surcharge
- Shows:
  - total SDLT payable
  - effective tax rate
  - per-band tax breakdown in plain language
- Uses config-driven tax rules from `config/sdlt.php` (no hardcoded rates in calculation logic)

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+ and npm

## Run in under 5 minutes (fresh clone)

From the project root:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan serve
```

Then open:

- [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Note on frontend assets

This app currently uses Bootstrap via CDN in Blade, so no Vite build is required to use the calculator page.

If you later add Vite-managed frontend assets, run one of:

```bash
npm run dev
```

or

```bash
npm run build
```

## Quick sanity-check inputs (math checks)

Use these examples in the UI to verify totals quickly:

1) **Standard residential**
- Input:
  - Price: `300000`
  - First-time buyer: `No`
  - Additional property: `No`
- Expected:
  - Scenario: `Standard residential rates`
  - Total SDLT: `GBP 5,000.00`
  - Effective rate: `1.67%`

2) **First-time buyer relief (within cap)**
- Input:
  - Price: `400000`
  - First-time buyer: `Yes`
  - Additional property: `No`
- Expected:
  - Scenario: `First-time buyer relief rates`
  - Total SDLT: `GBP 5,000.00`
  - Effective rate: `1.25%`

3) **First-time buyer selected but over relief cap**
- Input:
  - Price: `600000`
  - First-time buyer: `Yes`
  - Additional property: `No`
- Expected:
  - Scenario: `Standard residential rates` (relief not applied above cap)
  - Total SDLT: `GBP 20,000.00`
  - Effective rate: `3.33%`

4) **Additional property surcharge**
- Input:
  - Price: `300000`
  - First-time buyer: `No`
  - Additional property: `Yes`
- Expected:
  - Scenario: `Additional property rates`
  - Total SDLT: `GBP 20,000.00`
  - Effective rate: `6.67%`

## Validation behavior

- Price must be numeric and greater than 0
- Invalid combination is rejected:
  - first-time buyer = `Yes` and additional property = `Yes`
- Errors are shown in-page with user-friendly messages

## Tests

Run calculator tests:

```bash
php artisan test --compact tests/Unit/SdltCalculatorServiceTest.php tests/Feature/SdltCalculatorFeatureTest.php
```

Run full test suite:

```bash
php artisan test --compact
```

## Project structure (relevant files)

- Routes: `routes/web.php`
- Controller: `app/Http/Controllers/SdltCalculatorController.php`
- Calculator service: `app/Services/SdltCalculatorService.php`
- Tax config: `config/sdlt.php`
- View: `resources/views/sdlt-calculator.blade.php`
- Tests:
  - `tests/Unit/SdltCalculatorServiceTest.php`
  - `tests/Feature/SdltCalculatorFeatureTest.php`

## Deployment Notes

During deployment on cPanel shared hosting, the following issues were addressed:

* **DNS Resolution:**
  Configured an A record for `sdlt-calculator.kossikponvi.com` to point to the hosting server IP.

* **cPanel Domain Mapping:**
  Mapped the subdomain to the Laravel project directory in the hosting file system.

* **PHP Extensions (`pdo_mysql`):**
  Enabled required PHP extensions (`pdo`, `pdo_mysql`, `mysqli`) via cPanel.

* **Laravel Configuration (`.env`, Sessions):**
  Switched to file-based sessions to remove database dependency:

  ```
  SESSION_DRIVER=file
  CACHE_DRIVER=file
  ```

* **Apache Rewrite Logic:**
  Configured `.htaccess` to route all requests through the `/public` directory, ensuring proper Laravel routing.

## Live Demo

[View the SDLT Calculator](https://sdlt-calculator.kossikponvi.com) or visit `https://sdlt-calculator.kossikponvi.com`
