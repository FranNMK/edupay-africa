# Operations Guide

This document covers deployment procedures, environment management, database migrations, and backup/restore for EduPay Africa.

---

## Environments

| Environment | Purpose                              | Branch / tag   |
|-------------|--------------------------------------|----------------|
| Local dev   | Individual developer workstations    | feature/*      |
| Staging     | Pre-production validation            | develop / main |
| Production  | Live end-user traffic                | tagged release |

---

## Deployment checklist

### Pre-deploy
1. All tests pass locally (`./vendor/bin/phpunit --no-coverage`).
2. CI is green on the pull request.
3. `.env` for the target environment is up-to-date and **not** committed to source control.
4. A database backup has been taken (see [Backup & restore](#backup--restore)).
5. All pending SQL migrations have been reviewed.

### Deploy steps
1. Pull latest code to the server (or push via the CI pipeline):
   ```bash
   git pull origin main
   ```
2. Install / update PHP dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Apply any pending database migrations in order (see [Database migrations](#database-migrations)).
4. Verify the application loads and key flows work via a quick smoke test.
5. Monitor error logs for 15–30 minutes post-deploy.

---

## Environment variables

Production credentials **must** be set as real environment variables (e.g., via server config, Docker secrets, or a secrets manager) rather than a `.env` file.

Example for Apache `VirtualHost`:
```apache
SetEnv DB_HOST     db.internal
SetEnv DB_NAME     edupay_africa
SetEnv DB_USER     edupay_app
SetEnv DB_PASS     <strong-password>
SetEnv DB_CHARSET  utf8mb4
```

Example for NGINX + PHP-FPM (`fastcgi_param` block):
```nginx
fastcgi_param DB_HOST    db.internal;
fastcgi_param DB_NAME    edupay_africa;
fastcgi_param DB_USER    edupay_app;
fastcgi_param DB_PASS    <strong-password>;
fastcgi_param DB_CHARSET utf8mb4;
```

---

## Database migrations

Migration files live in `database/` and must be applied in sequence. Track which files have already been applied to avoid duplicate runs.

| File | Purpose | When to apply |
|------|---------|---------------|
| `schema.sql` | Full schema bootstrap | New databases only |
| `demo_requests_migration.sql` | Demo request tables | After initial schema |
| `demo_requests_approval_migration.sql` | Approval workflow | After demo_requests migration |
| `demo_requests_approval_patch_v2.sql` | Legacy patch | Only if older approval migration was applied |

Apply via MySQL CLI:
```bash
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql
```

> **Rollback:** There are currently no automated rollback scripts. Before applying each migration in production, manually note the reverse DDL steps, or take a database snapshot.

---

## Backup & restore

### Create a backup
```bash
mysqldump \
  -h "$DB_HOST" \
  -u "$DB_USER" -p"$DB_PASS" \
  --single-transaction \
  --routines \
  --triggers \
  "$DB_NAME" > "backup_$(date +%Y%m%d_%H%M%S).sql"
```

Store backups off-site (e.g., S3, Google Cloud Storage). Retain at minimum:
- Daily backups for 14 days
- Weekly backups for 3 months

### Restore from backup
```bash
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < backup_<timestamp>.sql
```

> Test restore procedures on staging periodically to verify backups are usable.

---

## Session & cookie settings (production)

Add the following to `php.ini` or your server's PHP configuration for hardened session handling:

```ini
session.cookie_httponly = 1
session.cookie_secure   = 1   ; requires HTTPS
session.cookie_samesite = Lax
session.use_strict_mode = 1
session.gc_maxlifetime  = 1800
```

---

## Logging

Currently, errors are handled via PHP's default error logging. For production:

1. Set `display_errors = Off` and `log_errors = On` in `php.ini`.
2. Configure `error_log` to a writable path outside the webroot.
3. Rotate logs with `logrotate` or equivalent.

Future work (Phase 2): structured JSON request logs with `request_id`, `user_id`, and `institution_id` fields.

---

## Health check

A simple health endpoint is not yet implemented. Until it is, use an external uptime monitor (e.g., UptimeRobot) to HTTP-GET the login page and alert on non-200 responses.
