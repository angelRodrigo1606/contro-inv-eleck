# AGENTS.md — contro-inv-eleck

This file is a quick reference for AI coding agents working on this project. It describes the current state of the codebase, the technology stack, how to build/test/run the application, and the conventions in use.

## Project overview

- **Project name:** `contro-inv-eleck` (Composer package name is still `laravel/laravel`).
- **Purpose:** Web application for controlling the inventory of an electronic-products warehouse.
- **Domain requirements:** The intended functionality is documented in `Ficha-Tecnica-Requerimientos-Funcionales.md` (Spanish). It covers product/catalog management, categories, suppliers, stock movements (entries/exits), low-stock alerts, reports, and role-based users (`Administrador` / `Empleado`).
- **Current state:** The project is a fresh Laravel 13.x installation. Only the default `User` model, the default migrations, a single `welcome` route, and example Pest tests exist. The inventory domain modules described in the requirements document are **not yet implemented**.
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
  Http/Controllers/Controller.php   # Base controller
  Models/User.php                   # Default User authenticatable
  Providers/AppServiceProvider.php
bootstrap/
  app.php                           # Application bootstrap, registers web/console routes and /up health route
  providers.php                     # Only AppServiceProvider currently
config/                             # Standard Laravel configuration
database/
  factories/UserFactory.php
  migrations/0001_01_01_000000_*   # Default users/password_reset_tokens/sessions, cache, jobs
  seeders/DatabaseSeeder.php
public/
  index.php                         # Web entry point
resources/
  css/app.css                       # Tailwind v4 entry
  js/app.js                         # Empty JS entry
  views/welcome.blade.php           # Default welcome view
routes/
  web.php                           # Currently only `/` -> welcome
  console.php                       # Artisan command definitions
tests/
  Feature/ExampleTest.php
  Unit/ExampleTest.php
  Pest.php                          # Pest configuration, extends Tests\TestCase for Feature
  TestCase.php
```

Autoloading follows PSR-4:

- `App\` → `app/`
- `Database\Factories\` → `database/factories/`
- `Database\Seeders\` → `database/seeders/`
- `Tests\` → `tests/`

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
- `tests/Pest.php` extends `Tests\TestCase` for the `Feature` suite. `RefreshDatabase` is commented out by default; enable it when tests need a clean database.
- Two example tests exist and pass out of the box.

## Code style guidelines

- Follow Laravel Pint (PSR-12 + Laravel conventions). Run `vendor/bin/pint` before committing.
- Respect `.editorconfig`: UTF-8, LF line endings, 4-space indentation (2 spaces for YAML).
- Use PSR-4 namespaces and directory layout.
- Prefer PHP 8 attribute-style model configuration when appropriate (e.g., `#[Fillable([...])]`, `#[Hidden([...])]`) as already used in `app/Models/User.php`.
- Blade views use the `@vite` directive and `@fonts` directive.
- Tailwind CSS v4 is imported with `@import 'tailwindcss';` in `resources/css/app.css`.
- Keep code in English (class names, variables, comments) unless the domain concept itself is Spanish-only and already reflected in the requirements document.

## Security considerations

- `.env` is git-ignored. Use `.env.example` as the template. Never commit real credentials or `APP_KEY`.
- Generate a fresh `APP_KEY` for each environment (`php artisan key:generate`).
- Passwords are hashed via bcrypt (see the `hashed` cast in `User.php`).
- CSRF protection is enabled on web routes by default.
- No authentication starter kit (Breeze/Jetstream) is installed yet. The requirements document mentions it, but it will need to be added if required.
- For production: set `APP_ENV=production`, `APP_DEBUG=false`, serve only over HTTPS, and keep dependencies updated.

## Deployment notes

- Standard Laravel deployment: the web server document root must point to the `public/` directory.
- Run `php artisan storage:link` if the application uses the public disk.
- Run migrations in production with `php artisan migrate --force`.
- Build frontend assets with `npm run build` before deploying; the generated `public/build` directory is required in production.
- If using queues, run a queue worker and configure the scheduler (`php artisan schedule:run` via cron).
- There is no Docker or CI configuration in the repository yet.

## Planned domain modules (from requirements)

The requirements document describes the following modules to be built:

- **Product CRUD** with SKU (immutable), category, supplier, price, initial quantity, minimum stock, and soft deletes.
- **Category and supplier CRUD**, with restrictions on deletion when products are linked.
- **Stock movements** (entries/exits) that automatically update product stock and record date, type, quantity, responsible user, and optional reference.
- **Low-stock alerts** when a product reaches its configured minimum stock.
- **Reports** (filterable by date range, product, category, supplier) with PDF/Excel export and a consolidated dashboard.
- **Role-based users**: `Administrador` (full access) and `Empleado` (limited access to products, movements, and reports).

These modules are not implemented. Build them following the existing Laravel conventions, PSR-4 namespaces, Pest tests, and Tailwind-based Blade views.

## Key files to consult

- `composer.json` — PHP dependencies and Composer scripts.
- `package.json` — Node scripts and frontend dependencies.
- `vite.config.js` — Vite, Tailwind, and font configuration.
- `phpunit.xml` — Test environment and suites.
- `.env.example` — Environment variable template.
- `Ficha-Tecnica-Requerimientos-Funcionales.md` — Domain requirements (Spanish).
