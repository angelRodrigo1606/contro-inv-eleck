# AGENTS.md — contro-inv-eleck

This file is a quick reference for AI coding agents working on this project. It describes the current state of the codebase, the technology stack, how to build/test/run the application, and the conventions in use.

## Project overview

- **Project name:** `contro-inv-eleck` (Composer package name is still `laravel/laravel`).
- **Purpose:** Web application for controlling the inventory of an electronic-products warehouse.
- **Domain requirements:** The intended functionality is documented in `Ficha-Tecnica-Requerimientos-Funcionales.md` (Spanish). It covers product/catalog management, categories, suppliers, stock movements (entries/exits), low-stock alerts, reports, and role-based users (`Administrador` / `Empleado`).
- **Current state:** The inventory domain modules are implemented. The application uses a **Repository pattern** on top of Eloquent: controllers depend on services, services depend on repository interfaces, and Eloquent implementations are bound in `AppServiceProvider`.
- **Default locale:** `es` (`APP_LOCALE=es`). Spanish translation files live in `lang/es/` and `lang/es.json`.

## Technology stack

- **Backend framework:** Laravel Framework 13.15.0 (requires PHP `^8.3`; current environment runs PHP 8.5.0).
- **Frontend build:** Vite 8, `laravel-vite-plugin` 3.1, Tailwind CSS 4, `@tailwindcss/vite`.
- **Font:** Instrument Sans loaded via Bunny Fonts through `laravel-vite-plugin/fonts`.
- **Frontend entry points:** `resources/css/app.css` and `resources/js/app.js`.
- **Testing:** Pest PHP 4.7 with `pest-plugin-laravel` 4.1, running on top of PHPUnit. Also includes Mockery and Faker.
- **Code style / linting:** Laravel Pint 1.27.
- **Development helpers:** Laravel Pail 1.2.5, Laravel PAO 1.0.6, `nunomaduro/collision`.
- **Process runner:** `concurrently` is used by the `composer dev` script.
- **Database:** Default connection in `.env.example` is MySQL. `config/database.php` also supports SQLite (default fallback), MariaDB, PostgreSQL, SQL Server, and Redis.

## Project structure

```text
app/
  Http/Controllers/              # Web controllers (domain + auth)
  Http/Middleware/RoleMiddleware.php
  Http/Requests/                 # Form requests
  Models/                        # User, Category, Supplier, Product, StockMovement, LowStockAlert
  Notifications/LowStockNotification.php
  Observers/StockMovementObserver.php
  Providers/AppServiceProvider.php
  Repositories/                  # Repository pattern layer
    Contracts/                   # Repository interfaces
    Eloquent/                    # Eloquent implementations
  Services/                      # Business logic layer
    Catalog/                     # CategoryService, SupplierService
    Exceptions/                  # Domain exceptions
    Inventory/                   # ProductService, StockMovementService, StockAlertService
    Reporting/                   # ReportService, ReportExporter, DashboardService
    Users/                       # UserService, ProfileService
  View/Components/
bootstrap/
  app.php                        # Application bootstrap, registers routes and middleware
  providers.php                  # Registers AppServiceProvider
config/                          # Standard Laravel configuration
database/
  factories/                     # Model factories
  migrations/                    # Default + inventory domain migrations
  seeders/DatabaseSeeder.php
public/
  index.php                      # Web entry point
resources/
  css/app.css                    # Tailwind v4 entry
  js/app.js                      # Empty JS entry
  views/                         # Blade views
routes/
  web.php                        # Application routes
  auth.php                       # Laravel Breeze auth routes
  console.php                    # Artisan command definitions
tests/
  Feature/                       # Pest feature tests
  Unit/ExampleTest.php
  Pest.php                       # Pest configuration with RefreshDatabase for Feature
  TestCase.php
```

Autoloading follows PSR-4:

- `App\` → `app/`
- `Database\Factories\` → `database/factories/`
- `Database\Seeders\` → `database/seeders/`
- `Tests\` → `tests/`

## Architecture

The application follows a layered architecture:

```text
Routes → Controller → Service → Repository Interface → Eloquent Repository → Model
```

- **Controllers** handle HTTP concerns and delegate to services.
- **Services** contain business rules, exceptions, and orchestration.
- **Repositories** abstract all data access behind interfaces. Eloquent implementations live in `App\Repositories\Eloquent` and are bound to their interfaces in `AppServiceProvider::register()`.

Do not access Eloquent models directly from controllers or services; use the injected repository interfaces.

## Build, development, and run commands

| Command | What it does |
| --- | --- |
| `composer install` | Install PHP dependencies. |
| `cp .env.example .env` | Create environment file (the setup script does this automatically). |
| `php artisan key:generate` | Generate `APP_KEY` for encryption. |
| `php artisan migrate` | Run database migrations. |
| `npm install` | Install Node dependencies (`.npmrc` sets `ignore-scripts=true`). |
| `npm run build` | Production Vite build; outputs to `public/build`. |
| `npm run dev` | Start the Vite dev server. |
| `php artisan serve` | Start the local PHP development server. |
| `composer dev` | Run `php artisan serve`, `queue:listen`, `pail`, and `npm run dev` concurrently. |
| `composer setup` | One-shot setup: installs PHP/Node deps, creates `.env`, generates key, migrates, and builds assets. |
| `composer test` | Clears config and runs `php artisan test`. |
| `php artisan test` or `vendor/bin/pest` | Run the Pest/PHPUnit test suite. |
| `vendor/bin/pint` | Lint and auto-format PHP code. |

## Testing instructions

- Test configuration is in `phpunit.xml`.
- Test suites: `Unit` (`tests/Unit`) and `Feature` (`tests/Feature`).
- The testing environment forces `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `QUEUE_CONNECTION=sync`, array cache/session, etc.
- Base test class: `Tests\TestCase`.
- `tests/Pest.php` extends `Tests\TestCase` for the `Feature` suite and applies `RefreshDatabase`.
- Feature tests cover auth, categories, suppliers, products, stock movements, low-stock alerts, reports, users, and profile.

## Code style guidelines

- Follow Laravel Pint (PSR-12 + Laravel conventions). Run `vendor/bin/pint` before committing.
- Respect `.editorconfig`: UTF-8, LF line endings, 4-space indentation (2 spaces for YAML).
- Use PSR-4 namespaces and directory layout.
- Prefer PHP 8 attribute-style model configuration when appropriate (e.g., `#[Fillable([...])]`, `#[Hidden([...])]`) as already used in `app/Models/User.php`.
- Blade views use the `@vite` directive and `@fonts` directive.
- Tailwind CSS v4 is imported with `@import 'tailwindcss';` in `resources/css/app.css`.
- Keep code in English (class names, variables, comments) unless the domain concept itself is Spanish-only and already reflected in the requirements document.
- Type-hint repository interfaces in service and controller constructors.

## Security considerations

- `.env` is git-ignored. Use `.env.example` as the template. Never commit real credentials or `APP_KEY`.
- Generate a fresh `APP_KEY` for each environment (`php artisan key:generate`).
- Passwords are hashed via bcrypt (see the `hashed` cast in `User.php`).
- CSRF protection is enabled on web routes by default.
- Laravel Breeze is installed for authentication scaffolding.
- For production: set `APP_ENV=production`, `APP_DEBUG=false`, serve only over HTTPS, and keep dependencies updated.

## Deployment notes

- Standard Laravel deployment: the web server document root must point to the `public/` directory.
- Run `php artisan storage:link` if the application uses the public disk.
- Run migrations in production with `php artisan migrate --force`.
- Build frontend assets with `npm run build` before deploying; the generated `public/build` directory is required in production.
- If using queues, run a queue worker and configure the scheduler (`php artisan schedule:run` via cron).
- There is no Docker or CI configuration in the repository yet.

## Key files to consult

- `composer.json` — PHP dependencies and Composer scripts.
- `package.json` — Node scripts and frontend dependencies.
- `vite.config.js` — Vite, Tailwind, and font configuration.
- `phpunit.xml` — Test environment and suites.
- `.env.example` — Environment variable template.
- `Ficha-Tecnica-Requerimientos-Funcionales.md` — Domain requirements (Spanish).
- `app/Repositories/Contracts/` — Repository contracts.
- `app/Repositories/Eloquent/` — Eloquent repository implementations.
- `app/Providers/AppServiceProvider.php` — Repository interface bindings and observer registration.
