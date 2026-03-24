# EduPay Africa Technical Review

## 1) Project purpose and domain
EduPay Africa is a web-based education-finance platform focused on school fee management for African institutions, with explicit positioning around Kenyan payment workflows (e.g., M-Pesa in copy) and lead-to-onboarding workflows for institutions. The current codebase supports three core tracks: user authentication/roles (admin, institution, parent, student), parent-student fee visibility, and demo-request capture/approval for institution onboarding.

## 2) Architecture and tech stack
- **Architecture style:** Monolithic PHP app with server-rendered pages in `public/`, domain classes in `src/`, and SQL migrations/scripts in `database/`.
- **Backend:** PHP (PDO-based data access, session auth, class wrappers like `User`, `Institution`, `DemoRequest`).
- **Database:** MySQL/InnoDB, with schema bootstrapping and additive migration files.
- **Frontend:** HTML/CSS with mostly inline styles and some static assets under `public/css` and `public/js`.
- **Tooling:** Minimal; no Composer manifest, no CI configuration, no containerization, and no automated test harness discovered.

## 3) Build, run, test, and deploy
### Local install/run
1. Provision PHP + MySQL (XAMPP/LAMP style setup is implied).
2. Create/import database using:
   - `database/schema.sql`
   - `database/demo_requests_migration.sql`
   - `database/demo_requests_approval_migration.sql`
   - optional `database/demo_requests_approval_patch_v2.sql` for legacy DB patching.
3. Configure DB credentials in `config/db.php`.
4. Serve the app with web root pointed at `public/`.
5. Access `/public/index.php` and `/public/login.php`.

### Test status (current)
- No formal unit/integration/e2e test framework found.
- Baseline validation today is static/manual smoke testing and PHP linting.

### Deployment status (current)
- No deployment automation, release pipeline, or environment templates (staging/production) were found.
- Current model appears to be manual SQL migration + file deployment.

## 4) Strengths and potential issues
### Strengths
- Uses PDO prepared statements in core data operations.
- Uses password hashing and verification (`password_hash`, `password_verify`).
- Includes session-based role checks in admin paths.
- Has incremental SQL migration files for onboarding flow evolution.

### Potential issues
- **Security:**
  - Hardcoded DB credentials pattern in config and default blank root password assumption.
  - Session cookie/security hardening flags are not centrally enforced.
  - CSRF protection is present in some admin flows but not consistently across auth/profile/forms.
  - Error handling in places swallows exceptions, reducing auditability.
- **Performance/scalability:**
  - Monolith with inline-heavy pages may become hard to optimize and cache.
  - No pagination strategy visible for potentially growing admin lists beyond simple table rendering.
  - No observed queue/async path for outbound onboarding notifications.
- **Accessibility:**
  - Several views are visually rich but lack explicit semantic/accessibility patterns consistency (landmarks, form labels/associations, keyboard flow checks, contrast audits).
- **Maintainability:**
  - Mixed presentation/business logic inside page controllers.
  - Limited central configuration and no dependency management manifest.
  - Sparse README and no contributor/deployment handbook.

## 5) Concrete improvement suggestions
### Short-term MVP enhancements (0–4 weeks)
- Add environment-based configuration (`.env`) and remove credential assumptions.
- Add CSRF tokens to all state-changing POST endpoints.
- Standardize output escaping with helper functions for all rendered user input.
- Introduce central auth middleware/check helpers to avoid repeated session logic.
- Add basic smoke tests and CI lint gates (PHP lint + coding standards).

### Code quality and tests (1–2 months)
- Introduce Composer + autoloading (PSR-4) and refactor `public/*.php` thin controllers.
- Add PHPUnit for domain/service classes (`User`, `DemoRequest`, `Institution`).
- Add integration tests for critical paths: login, parent fee view, demo approval lifecycle.
- Create DB migration discipline (single source migration order + rollback notes).

### Documentation improvements (immediate)
- Expand README to include prerequisites, setup, migrations, env vars, demo credentials, and troubleshooting.
- Add `docs/OPERATIONS.md` for deployment and backup/restore.
- Add `docs/SECURITY.md` for auth/session/incident response basics.

## 6) Phased plan
### Phase 0 — Minimal viable improvement path (Week 1–2)
- **Owner: Backend engineer** — centralize config via env variables, remove hardcoded secrets.
- **Owner: Full-stack engineer** — add CSRF + validation parity across POST routes.
- **Owner: QA engineer** — define and execute manual regression checklist for auth + demo approval.
- **Owner: Tech lead** — publish updated README runbook.

### Phase 1 — Stabilization (Week 3–6)
- **Owner: Backend engineer** — introduce Composer autoloading and thin-controller refactor.
- **Owner: QA engineer** — implement PHPUnit baseline tests (auth + onboarding).
- **Owner: DevOps engineer** — add CI workflow for lint + tests on pull requests.

### Phase 2 — Scale readiness (Quarterly roadmap)
- **Owner: Architecture lead** — split domain services from presentation layer and formalize application service boundaries.
- **Owner: DevOps engineer** — add reproducible deployment (containerization + environment profiles).
- **Owner: Product + Engineering** — add observability (structured logs, audit trails, dashboard metrics), pagination/search for admin lists, and asynchronous notification pipeline.
