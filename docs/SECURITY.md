# Security Guide

This document describes the authentication model, session security configuration, known controls, and the incident response process for EduPay Africa.

---

## Authentication model

EduPay Africa uses **PHP session-based authentication** with bcrypt password hashing.

| Control | Implementation |
|---------|---------------|
| Password hashing | `password_hash($password, PASSWORD_BCRYPT)` / `password_verify()` |
| Session identity | `$_SESSION['user_id']`, `$_SESSION['role']` |
| Auth guard | `EduPay\Auth::requireLogin()` — redirects unauthenticated requests to `login.php` |
| Role guard | `EduPay\Auth::requireRole('admin')` — returns HTTP 403 for wrong role |
| Session isolation | One session per authenticated user; destroyed on logout |

---

## CSRF protection

All state-changing `POST` endpoints are protected with synchronizer tokens:

| Helper | Description |
|--------|-------------|
| `csrf_token()` | Returns (and generates) the session CSRF token |
| `csrf_field()` | Renders `<input type="hidden" name="csrf_token" value="...">` inside forms |
| `csrf_verify()` | Verifies `$_POST['csrf_token']` matches the session token using `hash_equals()` |

If you add a new form that changes state (create, update, delete), you **must** call `csrf_verify()` in the POST handler and include `<?php echo csrf_field(); ?>` inside the `<form>`.

---

## Output escaping

All user-controlled values rendered in HTML are escaped via the `h()` helper:

```php
echo h($user_supplied_value);
```

`h()` calls `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` and accepts any type.

> **Never** `echo` raw `$_POST`, `$_GET`, `$_SESSION`, or database values directly into HTML output.

---

## Input validation

- Allowlists are used for enumerated fields (e.g., `school_type`, `preferred_contact`, `approval_status`).
- Numeric IDs are cast to `(int)` before use in queries.
- PDO prepared statements are used for all database interactions — no string interpolation of user data into SQL.

---

## Session hardening

Set the following in `php.ini` for production deployments:

```ini
session.cookie_httponly = 1    ; Prevents JavaScript access to the session cookie
session.cookie_secure   = 1    ; Transmit cookie over HTTPS only
session.cookie_samesite = Lax  ; Limits cross-site cookie sending
session.use_strict_mode = 1    ; Reject unrecognised session IDs
session.gc_maxlifetime  = 1800 ; Expire idle sessions after 30 minutes
```

---

## Known limitations (to address in upcoming phases)

| Item | Priority | Phase |
|------|----------|-------|
| No rate limiting on login endpoint | High | 1 |
| Session fixation protection (regenerate ID on login) | High | 1 |
| No HTTPS enforcement at application layer | High | 1 |
| No audit log for admin and payment actions | Medium | 2 |
| No account lockout after repeated failed logins | Medium | 2 |
| No two-factor authentication | Low | 3 |

---

## Responsible disclosure

If you discover a security vulnerability in EduPay Africa, please report it responsibly:

1. **Do not** open a public GitHub issue for security vulnerabilities.
2. Email the maintainer at the address listed in the repository with a clear description of the vulnerability and steps to reproduce.
3. Allow a reasonable time (up to 14 days) to patch before public disclosure.

---

## Incident response

### Suspected account compromise
1. Immediately invalidate active sessions by restarting the PHP session handler or rotating the session secret.
2. Reset the affected user's password hash directly in the database.
3. Review access logs for the affected `user_id` over the preceding 48 hours.
4. Notify the affected user.

### Suspected SQL injection or data breach
1. Take the application offline or block the affected endpoint immediately.
2. Preserve raw access logs before rotating or deleting.
3. Assess which data tables may have been accessed.
4. Follow applicable regulatory notification requirements (e.g., Kenya Data Protection Act).
5. Patch the vulnerability, run a full security review, and re-deploy.

### Compromised admin credential
1. Revoke the session (`DELETE FROM sessions` or restart PHP-FPM).
2. Change the admin password hash in the database.
3. Review all admin actions in the access log for the preceding 7 days.
4. Rotate any secrets (DB password, API keys) the admin may have had access to.
