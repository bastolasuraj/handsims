## Production Security & Readiness Checklist

### Critical Issues to Address
- Disable the public log viewer (`index.php` route `/public-logs` and `app/Controllers/PublicLogController.php`); it sets a fake admin session and exposes activity logs without auth.
- Lock down or remove the demo seeding endpoint (`/dashboard/seed-demo`, `DashboardController::seedDemo`); currently any authenticated user can run it and it lacks CSRF.
- Add CSRF protection to all state-changing endpoints: auth login/register, report generation/export, log clear, notification mark (single/all), and bulk CSV imports for products/stock.
- Constrain bulk CSV uploads (content type/size/header validation) before processing; they currently accept any file.
- Restrict the database viewer to admins only or remove it for production (`App/Controllers/DatabaseController.php`).

### Hardening Actions
- Align session cookie settings with env (use configured name, domain, secure, SameSite=Strict where possible) instead of the hard-coded `HANDSIMS_SESSION` (`config/session.php`).
- Add security headers (HSTS, CSP, optionally XSS-Protection) and use SRI for CDN assets loaded in `app/views/layouts/main.php`.
- Ensure writable directories are limited to `logs/` and `public/uploads/`; keep error logging on and review rotation.

### Files to Keep for Prod
- Application code under `app/`, configs in `config/`, DB schema `database/schema.sql`.
- Public assets in `public/`, entrypoints `index.php`, `404.php`, cron `cron_check_inventory.php`, composer files, `.htaccess` files, `logs/` dir (content writable, not committed).

### Files/Routes Safe to Remove or Disable in Prod
- Public log viewer controller/route (`/public-logs`).
- Demo seeding route (`/dashboard/seed-demo`) if not needed live.
- Database viewer route/views if schema inspection is not required in prod.
- Sample CSVs in `public/samples/` (if not user-facing).
- Reference images and the `unnecessaries/` folder (if present on server).
