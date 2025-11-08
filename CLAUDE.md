# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Personal expense tracking application built with Laravel 10, Inertia.js, and Vue 3. Tracks expenses, income, receipts, and categories with monthly/yearly summaries. Uses the Money PHP library for proper currency handling (all amounts stored as cents in USD).

## Development Environment

### Docker-Based Development

The application runs in Docker containers. All commands should be executed via Docker:

```bash
# Start containers
make up

# Stop containers
make down

# PHP/Laravel commands
docker-compose exec -u $(id -u):$(id -g) php-fpm sh -c "php artisan [command]"

# Composer commands
make composer c="install"
make composer c="require vendor/package"
make composer c="update"

# Access shell in PHP container
make shell

# Laravel Tinker REPL
make tinker

# Fresh database with seed data
make dbfresh
```

### Frontend Development

```bash
# Install npm dependencies (runs in node container)
make npminstall

# Run Vite dev server (with HMR on port 5173)
make vite

# Build for production
docker-compose run --rm node sh -c "npm run build"

# Lint JavaScript/Vue files
docker-compose run --rm node sh -c "npm run lint"
```

### Testing

```bash
# Run all tests (PHPUnit/Pest)
docker-compose exec php-fpm sh -c "./vendor/bin/pest"

# Run specific test file
docker-compose exec php-fpm sh -c "./vendor/bin/pest tests/Feature/ExpenseTest.php"

# Run specific test
docker-compose exec php-fpm sh -c "./vendor/bin/pest --filter test_name"
```

### Code Quality

```bash
# Format PHP code with Laravel Pint
make pint

# ESLint for JavaScript/Vue
docker-compose run --rm node sh -c "npm run lint"
```

## Architecture

### Backend Structure

**Core Models:**
- `Expense`: Main expense records with money handling, receipt relationships, and Scout search
- `Income`: Income tracking with earned/unearned classification
- `Category`: User-defined expense categories (each user gets a default "Uncategorized")
- `Receipt`: File attachments for expenses stored in S3-compatible storage
- `User`: Authentication via Laravel Breeze

**Key Services:**
- `ExpenseService`: Aggregates monthly/yearly totals, calculates income vs expenses

**Money Handling:**
- All amounts stored as integers (cents) in database
- Uses `moneyphp/money` library for calculations
- `DecimalMoneyParser` for input (converts "123.45" → 12345 cents)
- `IntlMoneyFormatter` for output (formats with locale-specific currency symbols)
- Amount accessors/mutators in models handle conversion automatically

**Search:**
- Laravel Scout integration on `Expense` and `Income` models
- Configure search driver in `.env` (SCOUT_DRIVER)

**Authentication & Authorization:**
- Laravel Breeze for authentication
- Policy-based authorization: `ExpensePolicy`, `CategoryPolicy`, `IncomePolicy`
- Custom gate: `access-application` (check implementation in `AuthServiceProvider`)

### Frontend Structure

**Tech Stack:**
- Vue 3 with Composition API
- Inertia.js for SPA-like experience without API
- TailwindCSS + Headless UI for components
- Heroicons for icons
- Chart.js + vue-chartjs for visualizations
- Pinia for state management
- Vueuse for composables

**Directory Structure:**
- `resources/js/Pages/` - Inertia page components organized by feature (Auth, Dashboard, Expenses, Incomes, Categories, Profile)
- `resources/js/Components/` - Reusable Vue components
- `resources/js/Layouts/` - Layout wrappers
- `resources/js/Stores/` - Pinia stores
- `resources/js/Composables/` - Shared composition functions

**Routing:**
- Routes in `routes/web.php` using Inertia rendering
- Ziggy package provides Laravel routes to JavaScript

### Database

**Migrations:**
Database schema in `database/migrations/`:
- Users, password resets, personal access tokens (Laravel defaults)
- Categories: user-scoped with name and color
- Expenses: with transaction_date, effective_date, amount (cents), currency, category, payment method, business expense flag, notes (markdown)
- Receipts: file storage paths linked to expenses
- Incomes: with payment_date, effective_date, amount (cents), currency, earned income flag, notes (markdown)

**Important:**
- All date fields use `effective_date` for reporting/calculations and `transaction_date`/`payment_date` for record-keeping
- Notes fields support markdown (rendered with `Str::markdown()`)

### Business Logic

**Default Category Creation:**
- Event: `UserCreated` → Listener: `CreateDefaultCategory`
- Automatically creates "Uncategorized" category for new users

**Dashboard Summaries:**
- `ExpenseService::getMonthlyTotals()` calculates yearly and monthly totals
- Returns expenses, income, difference, and loss indicator
- Uses effective_date for all calculations

**Receipt Storage:**
- Path pattern: `user_{id}/{year}/{month}/` (see `Expense::getReceiptStoragePath()`)
- Storage driver configured via `FILESYSTEM_DISK` (default: S3)

## Common Patterns

### Creating New Features

1. **Add database migration** if schema changes needed
2. **Create/update model** with proper casts, relationships, accessors
3. **Add policy** for authorization if needed
4. **Create controller** with validation via Form Requests
5. **Add routes** in `routes/web.php` with appropriate middleware
6. **Create Inertia page component** in `resources/js/Pages/`
7. **Update tests** in `tests/Feature/`

### Working with Money

Always use Money objects for calculations:

```php
// In models - accessor handles conversion
protected function amount(): Attribute
{
    return Attribute::make(
        set: fn (string $value) => app(DecimalMoneyParser::class)
            ->parse($value, new Currency('USD'))->getAmount()
    );
}

// In services - use Money for math
$total = $collection->reduce(
    fn (Money $carry, Model $item) => $carry->add(Money::USD($item->amount)),
    Money::USD(0)
);
```

### Enums

Use ArchTech Enums package for backed enums with helpful traits:
- `InvokableCases`, `Names`, `Options`, `Values`
- Add `label()` method for display names
- See `PaymentMethod` enum for reference

## Configuration Notes

- **Environment:** Copy `.env.example` to `.env` and configure
- **Docker:** Service names use `COMPOSE_PROJECT_NAME` environment variable
- **Vite HMR:** Configured for Docker with host '0.0.0.0' and port 5173
- **Testing:** Uses SQLite in-memory database (configured in phpunit.xml)
- **Mail:** Uses Mailpit for development (accessible at localhost:8025)
