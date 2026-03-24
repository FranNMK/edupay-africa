# EduPay Africa

> Digital platform for school fee management in Africa.

EduPay Africa helps parents, institutions, and administrators manage school fee collection, onboarding, and reconciliation — with a current focus on Kenyan institutions and M-Pesa payment workflows.

---

## Table of contents

1. [Prerequisites](#prerequisites)
2. [Quick start (local)](#quick-start-local)
3. [Environment variables](#environment-variables)
4. [Database setup](#database-setup)
5. [Project structure](#project-structure)
6. [Running tests](#running-tests)
7. [Coding standards & CI](#coding-standards--ci)
8. [Demo credentials](#demo-credentials)
9. [Troubleshooting](#troubleshooting)
10. [Further reading](#further-reading)

---

## Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP         | ≥ 8.1   |
| MySQL       | ≥ 8.0   |
| Composer    | ≥ 2.x   |
| Web server  | Apache (mod_rewrite) or NGINX |

A XAMPP / LAMP / WAMP stack works for local development.

---

## Quick start (local)

```bash
# 1. Clone the repository
git clone https://github.com/FranNMK/edupay-africa.git
cd edupay-africa

# 2. Install PHP dependencies
composer install

# 3. Configure environment variables
cp .env.example .env
# Edit .env with your local DB credentials

# 4. Create and seed the database (see Database setup below)

# 5. Point your web server root at the project root (Apache will rewrite to public/)
#    or start PHP's built-in server:
php -S localhost:8080 -t public
```

Open `http://localhost:8080` in your browser.

---

## Environment variables

Copy `.env.example` to `.env` and update as needed:

| Variable     | Default         | Description                      |
|--------------|-----------------|----------------------------------|
| `DB_HOST`    | `localhost`     | MySQL host                       |
| `DB_NAME`    | `edupay_africa` | Database name                    |
| `DB_USER`    | `root`          | Database user                    |
| `DB_PASS`    | *(empty)*       | Database password                |
| `DB_CHARSET` | `utf8mb4`       | Connection character set         |

> **Never commit `.env` to version control.** The `.gitignore` already excludes it.

---

## Database setup

Run migration files in this order against your MySQL database:

```sql
-- 1. Bootstrap the full schema (run once on a fresh database)
SOURCE database/schema.sql;

-- 2. Demo request workflow tables
SOURCE database/demo_requests_migration.sql;

-- 3. Approval workflow columns
SOURCE database/demo_requests_approval_migration.sql;

-- 4. (Optional) Legacy patch — only if you ran an older version of step 3
SOURCE database/demo_requests_approval_patch_v2.sql;
```

Using XAMPP, import each file via **phpMyAdmin → Import**, or run via the MySQL CLI:

```bash
mysql -u root -p edupay_africa < database/schema.sql
```

---

## Project structure

```
edupay-africa/
├── .github/workflows/   GitHub Actions CI
├── config/
│   └── db.php           PDO connection (reads .env)
├── database/            SQL migration files
├── docs/
│   ├── OPERATIONS.md    Deployment & backup guide
│   └── SECURITY.md      Security policies & incident response
├── public/              Web root (controllers + static assets)
│   ├── css/
│   ├── images/
│   └── js/
├── src/                 Domain classes (PSR-4: EduPay\)
│   ├── Auth.php         Centralised session/role guards
│   ├── DemoRequest.php
│   ├── helpers.php      h(), csrf_token(), csrf_field(), csrf_verify()
│   ├── Institution.php
│   └── User.php
├── tests/               PHPUnit test suite
├── .env.example         Environment variable template
├── composer.json
└── phpunit.xml
```

---

## Running tests

```bash
# Run the full PHPUnit suite (uses SQLite in-memory — no database server needed)
./vendor/bin/phpunit --no-coverage

# Run with coverage report (requires Xdebug or PCOV)
./vendor/bin/phpunit
```

---

## Coding standards & CI

The project follows **PSR-12** for all PHP source files.

```bash
# Check coding standards
./vendor/bin/phpcs --standard=PSR12 src/

# Auto-fix fixable issues
./vendor/bin/phpcbf --standard=PSR12 src/
```

GitHub Actions runs **PHP lint → PHPCS → PHPUnit** on every push and pull request (see `.github/workflows/ci.yml`).

---

## Demo credentials

> These are **seed credentials for local development only**. Change all passwords before deploying to a shared or production environment.

| Role        | Email                    | Password   |
|-------------|--------------------------|------------|
| Admin       | `admin@edupay.africa`    | *(set via `utils/hash_tool.php` — see below)* |
| Parent      | `parent@example.com`     | *(self-registered)*                           |

To generate a bcrypt hash for a new password, use:

```bash
php utils/hash_tool.php
```

Then insert the hashed value directly into the `users` table via phpMyAdmin or MySQL CLI.

---

## Troubleshooting

| Symptom | Likely cause | Fix |
|---------|--------------|-----|
| Blank page or 500 error | PHP display errors disabled | Enable `display_errors = On` in `php.ini` for dev |
| "Table not found" | Missing migration | Re-run the relevant `database/*.sql` file |
| Login redirects back to login | Session not starting | Check `session.save_path` is writable; ensure no output before `session_start()` |
| CSRF token mismatch | Form submitted from stale page | Refresh the page and resubmit |
| `.env` not loading | Missing `vlucas/phpdotenv` | Run `composer install` |

---

## Further reading

- [`docs/OPERATIONS.md`](docs/OPERATIONS.md) — Deployment, backup, and restore procedures.
- [`docs/SECURITY.md`](docs/SECURITY.md) — Authentication model, session hardening, and incident response.
- [`TECHNICAL_REVIEW.md`](TECHNICAL_REVIEW.md) — Full technical audit and phased modernisation roadmap.

