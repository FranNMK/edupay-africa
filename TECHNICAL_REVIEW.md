# EduPay Africa Modern Redesign Plan (Actionable)

## Executive summary
This plan redesigns EduPay Africa from a functional PHP monolith into a modern, accessible, measurable web platform while preserving current business flows (authentication, parent fee visibility, and institution onboarding). It prioritizes **MVP modernization first** (high-impact UX and reliability wins), then iterates into scale-ready architecture and richer product experiences.

---

## 1) Target goals and success metrics

### UX outcomes (first 90 days)
- Reduce demo-request form drop-off by **25%**.
- Increase successful first-time task completion (login, fee lookup, request submission) to **>90%** in moderated usability tests.
- Reduce median clicks to key tasks by **30%**.

### Performance outcomes
- Core Web Vitals targets on 4G/mobile:
  - **LCP < 2.5s**, **INP < 200ms**, **CLS < 0.1**.
- Home/dashboard initial payload budget: **< 250KB compressed** (MVP), **< 180KB** (phase 2).
- API p95 latency for top endpoints: **< 300ms**.

### Accessibility outcomes
- Achieve **WCAG 2.2 AA** for all authenticated and public critical user journeys.
- 100% keyboard operability on primary flows.
- Accessibility CI gates (axe/lighthouse) on every PR.

### Scalability/reliability outcomes
- Support 10x current traffic baseline without major architecture rewrite.
- 99.9% uptime target (post phase 2).
- Audit-ready logging for admin and payment-related events.

---

## 2) Modern design system strategy

### Design tokens (single source of truth)
- Define platform tokens in Style Dictionary or Tokens Studio pipeline:
  - Color (semantic: `surface`, `text`, `primary`, `success`, `danger`)
  - Typography scale, spacing scale, radius, elevation, motion durations/easings
  - Breakpoints and container widths
- Export tokens to:
  - Figma variables
  - CSS custom properties / Tailwind theme config
  - JSON for programmatic usage

### Component system
- Build a reusable component library (button, input, select, card, modal, table, toast, tabs, breadcrumb, empty state).
- Use accessibility-first primitives (e.g., Radix UI or Headless UI) + internal wrappers.
- Each component ships with:
  - states (default/hover/focus/disabled/error/loading)
  - usage guidelines
  - accessibility checklist
  - visual regression snapshot

### Accessibility standards
- Keyboard focus ring standard and skip links.
- Semantic landmarks and heading hierarchy.
- Form error handling with `aria-describedby`, inline validation, and summary alerts.
- Contrast ratio >= 4.5:1 for normal text.

### Theming
- Brand theme + optional high-contrast mode + dark mode readiness.
- Semantic color tokens so rebrands do not require component rewrites.

### Responsive guidelines
- Mobile-first breakpoints.
- Layout grid system with reusable page templates:
  - public marketing page
  - dashboard shell
  - data table page
  - form-centric onboarding flow

---

## 3) Phased roadmap (MVP → iterative enhancements)

## Phase 0: Discovery & alignment (Week 1)
**Milestones**
- Product/engineering kickoff, success metric baseline, and analytics instrumentation map.
- UX audit + accessibility baseline + technical architecture inventory.
- Prioritized backlog with ROI scoring.

**Deliverables**
- Redesign brief + KPI dashboard spec.
- Information architecture draft.
- Risk register v1.

## Phase 1: MVP modernization (Weeks 2–6)
**Scope**
- Navigation redesign, improved forms, dashboard shell, core design tokens, top 10 reusable components.
- Introduce frontend app shell for key pages while keeping existing backend operational.
- Security and reliability hardening (CSRF parity, session settings, error monitoring).

**Milestones**
- Week 3: clickable high-fidelity prototype validated with 5–8 users.
- Week 4: component library alpha + coding standards.
- Week 5: beta release to internal users.
- Week 6: production MVP release.

**Deliverables**
- Figma kit + component docs.
- Frontend scaffold + CI checks.
- WCAG AA report for critical flows.
- Performance baseline and budget checks in CI.

## Phase 2: Product quality and scale foundation (Weeks 7–12)
**Scope**
- Data visualization improvements, role-based dashboards, notification center, onboarding funnel optimization.
- Introduce service boundaries and API contracts.
- Add observability stack, audit trails, and queue-backed async workflows.

**Milestones**
- Week 8: API contract freeze for v1.
- Week 10: role dashboards + analytics cards live.
- Week 12: reliability and performance hardening complete.

**Deliverables**
- API docs (OpenAPI).
- Automated test coverage targets met.
- SLO dashboard + alerting playbooks.

## Phase 3: Growth and advanced capabilities (Quarter 2+)
**Scope**
- Advanced reporting, self-serve institution onboarding wizard, localization, multi-tenant controls.
- Experimentation framework (A/B tests) for conversion optimization.

**Deliverables**
- Growth experiments roadmap.
- Feature flag governance.
- Multi-region readiness assessment.

---

## 4) Recommended architecture and tech choices

### Frontend
- **Framework:** Next.js (React + TypeScript) for SSR/ISR, routing, and modern DX.
- **Styling:** Tailwind CSS + design tokens; Storybook for component documentation.
- **State/data:** TanStack Query for server state; lightweight local state with Zustand.
- **Forms:** React Hook Form + Zod validation.
- **Charts:** ECharts or Recharts for dashboards.

### Backend
- Near-term: keep PHP backend and expose stable JSON endpoints for redesigned frontend.
- Mid-term options:
  1. Modernize PHP into Laravel/Symfony modules, or
  2. Introduce a Node/NestJS or Go service layer for new domain services.
- Use OpenAPI contracts to decouple frontend/backend release cycles.

### Data & async
- MySQL remains primary transactional store.
- Redis for cache/session/queues.
- Queue workers for onboarding notifications and heavy jobs.

### Deployment & operations
- Containerized deployment (Docker) with managed platform (Render/Fly.io/AWS ECS).
- Environments: dev/staging/prod with immutable builds.
- CI/CD: GitHub Actions (lint, tests, accessibility, perf budgets, deploy gates).

### Observability
- Logs: structured JSON logs (request_id, user_id, institution_id).
- Metrics: Prometheus/OpenTelemetry + Grafana dashboards.
- Errors: Sentry or equivalent.
- Tracing for p95 endpoint analysis.

---

## 5) Concrete UI/UX recommendations

### Navigation and IA
- Adopt a consistent app shell:
  - Top bar (global actions, profile, alerts)
  - Left nav (role-aware sections)
  - Page header (title, actions, breadcrumbs)
- Reduce menu depth and prioritize top 5 most-used tasks.

### Dashboards
- Role-specific dashboard templates:
  - Admin: approvals pipeline, institution growth, exceptions.
  - Parent: children balances, recent transactions, reminders.
  - Institution: fee collection trends, arrears, reconciliation status.
- Use progressive disclosure and clear empty/error/loading states.

### Forms
- Multi-step forms for long onboarding with save-draft.
- Inline validation + smart defaults + input masks for phone/currency.
- Confirmation and success states with “next best action”.

### Data visualization
- Use trend cards + spark lines for quick insights.
- Provide table/chart toggles with CSV export.
- Include filter chips and date-range presets.

### Interaction patterns
- Subtle motion (150–250ms) for transitions.
- Skeleton loading for perceived speed.
- Toast feedback for non-blocking success/error notifications.
- Respect reduced-motion user preferences.

---

## 6) Risk assessment and fallback plans

### Major decision: frontend migration to Next.js
- **Risk:** Team ramp-up and temporary velocity dip.
- **Fallback:** Start with hybrid approach (incremental route-by-route migration, keep PHP pages for non-critical paths).

### Major decision: backend service split
- **Risk:** API inconsistency and duplicated business logic.
- **Fallback:** contract-first API governance, shared domain validation package, and migration playbook.

### Major decision: design system investment
- **Risk:** Initial overhead delays feature output.
- **Fallback:** MVP token set + 10 high-value components first; expand only after adoption metrics.

### Delivery risk: limited QA bandwidth
- **Risk:** regressions in role-based workflows.
- **Fallback:** risk-based test matrix + automation of critical paths first.

---

## 7) Measurable deliverables by phase

### Phase 0
- UX audit report, baseline KPI dashboard, architecture map, prioritized backlog.

### Phase 1
- Token package v1, component library alpha (10 components), WCAG AA audit for top journeys, Lighthouse CI budgets, MVP navigation/dashboard/forms rollout.

### Phase 2
- API contracts + docs, E2E tests for critical flows, observability dashboards, queue-backed notifications, reliability SLOs.

### Phase 3
- Experimentation framework, advanced analytics modules, localization readiness, scalability validation report.

---

## Constraints needed from stakeholders (to tailor this plan)
Please confirm the following so the roadmap can be finalized into sprint-ready work:
1. Preferred stack direction: stay primarily PHP, or approve React/Next.js frontend migration?
2. Timeline target: desired MVP launch date (e.g., 6 weeks vs 12 weeks).
3. Budget/team: number of engineers, designer availability, QA/DevOps support.
4. Compliance/security requirements: data residency, audit, or financial regulations.
5. Branding constraints: existing brand guidelines, dark mode requirements, multilingual scope.
6. Hosting constraints: current provider, budget limits, and any vendor restrictions.
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
