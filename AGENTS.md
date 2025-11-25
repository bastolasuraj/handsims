# Repository Guidelines

Quick reference for contributing to the Health & Safety Inventory Management System. Keep changes small, well-documented, and aligned with the structure below.

## Project Structure & Module Organization
- Application code lives in `app/` with MVC-style layout: controllers in `app/Controllers`, models in `app/Models`, shared base classes in `app/Core`, and Blade-like view templates under `app/views`.
- Configuration is centralized in `config/` (`database.php`, `env.php`, `ldap.php`, etc.); avoid duplicating settings elsewhere.
- Public assets and the web root are in `public/` (CSS/JS/images/uploads). Logs live in `logs/` and should stay writable only where required.
- Database schema and reference scripts sit in `database/schema.sql` and `schema_refined.sql`; `cron_check_inventory.php` runs scheduled stock checks.

## Build, Test, and Development Commands
- Install dependencies: `composer install`.
- Run the app locally: `php -S localhost:8000 -t public` after `cp .env.example .env` and setting DB credentials.
- Static analysis: `composer analyse` (phpstan).
- Lint: `composer lint` / auto-fix with `composer lint:fix` (phpcs/phpcbf).
- Tests: `composer test` (phpunit; expects a `phpunit.xml` config — add one under project root if missing).

## Coding Style & Naming Conventions
- Follow PSR-12: 4-space indentation, braces on new lines, strict typing where practical.
- Controllers are `PascalCase` ending in `Controller.php`; models mirror table intent (e.g., `ProductModel.php`). Keep view folders matching controller names.
- Prefer dependency injection via constructors in controllers/models; keep business logic out of views.
- Use meaningful filenames for assets and migrations; keep config keys snake_case to match existing files.

## Testing Guidelines
- Add phpunit tests under `tests/` mirroring namespace `Tests\\`. Name files `*Test.php` and classes `*Test`.
- Aim for coverage on controllers, models, and helpers that touch inventory calculations, authentication, and notifications.
- Use fixtures or seed data scoped to each test; avoid relying on live databases unless intentionally hitting integration cases.

## Commit & Pull Request Guidelines
- Use concise, imperative commit messages (`Add stock transfer validation`). The history is short; set the pattern now.
- For PRs, include: purpose/impact summary, linked issue/reference, test/lint results, and any schema or config steps (`.env`, permissions).
- Provide screenshots or terminal output when UI or workflow behavior changes; document manual verification steps for critical flows (auth, stock movement, alerts).

## Security & Configuration Tips
- Never commit `.env` or credentials; rotate `SECURE_AUTH_KEY`/`SECURE_AUTH_SALT` per environment.
- Ensure writable paths are limited to `logs/` and `public/uploads/`; avoid new world-writable locations.
- Validate and sanitize user input in controllers; keep server-side validation in sync with view-layer checks noted in `SERVER_SIDE_VALIDATION.md`.
