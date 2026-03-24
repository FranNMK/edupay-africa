# EduPay Africa — Project Kickoff Guide

## Context used for this kickoff
This kickoff is based on the artifacts in `docs/`, including:
- `EduPay_Africa_Implementation_Starter_Pack.md.docx` (Day-1 setup and team kickoff flow)
- `EduPay_Africa_Strategy_Guide.md.docx` (market context, competitor landscape, and channel strategy)
- `EduPay_Africa_-_Feasibility_Analysis_Report.docx` (technical feasibility and execution caveats)

---

## 1) High-level project brief

### Goals
1. Digitize school fee collection and reconciliation for African institutions (starting with Kenya-focused flows).
2. Reduce fee-default friction through transparent balances, reminders, and easier payment tracking.
3. Enable institution acquisition with a measurable demo-request-to-onboarding pipeline.

### Scope (MVP)
- Public marketing + demo request capture.
- Role-based authentication (admin, institution, parent, student).
- Parent fee statement visibility per student.
- Admin workflow for request qualification and institution onboarding approval.
- Basic operational analytics dashboard for admin users.

### Success criteria
- Demo request conversion uplift: +20% within first 8 weeks.
- Parent task success rate (find student balance): >90%.
- Time-to-approve institution request: <24 hours median.
- Core Web Vitals on top pages: LCP <2.5s, CLS <0.1, INP <200ms.
- Accessibility conformance: WCAG 2.2 AA for critical journeys.

### MVP definition (strict)
MVP is complete when the 5 scope items above are in production with:
- baseline observability,
- automated smoke tests,
- deployment rollback procedure,
- security checks for auth/session/CSRF on all state-changing endpoints.

---

## 2) Tech stack recommendations (with rationale)

### Recommended target stack (modern, pragmatic)
- **Frontend:** Next.js + TypeScript
  - Rationale: SSR/ISR for performance, ecosystem maturity, maintainability.
- **UI:** Tailwind CSS + design tokens + Storybook
  - Rationale: fast iteration with systemized reusable components.
- **State/Data:** TanStack Query + lightweight local state (Zustand)
  - Rationale: strong server-state patterns and predictable cache behavior.
- **Forms/Validation:** React Hook Form + Zod
  - Rationale: performant forms and consistent validation schemas.
- **Backend (near-term):** Keep existing PHP endpoints, add stable JSON API facade
  - Rationale: de-risk migration and preserve delivery speed.
- **Backend (mid-term):** Laravel modules or service-layer split (NestJS/Go)
  - Rationale: clearer domain boundaries and better scaling pathways.
- **Data:** MySQL + Redis (cache/session/queues)
  - Rationale: strong transactional base with async capabilities.
- **Observability:** OpenTelemetry + Grafana + Sentry
  - Rationale: metrics, traces, and actionable error tracking.
- **CI/CD:** GitHub Actions + staged deploy gates
  - Rationale: enforce quality and safe promotions.

---

## 3) Architecture overview

### Core modules
1. **Identity & Access** — authentication, sessions, RBAC.
2. **Institution Onboarding** — demo request intake, approval, onboarding status.
3. **Fee Ledger & Statements** — fee structure, student balances, parent views.
4. **Admin Operations** — workflow queues, status transitions, operational dashboards.
5. **Notifications** — email/SMS/WhatsApp queue-backed delivery.
6. **Audit & Analytics** — event logs, KPI dashboards, compliance trails.

### Data flow (MVP)
1. User submits demo request -> persisted in `demo_requests`.
2. Admin qualifies/approves request -> institution record created/linked.
3. Parent logs in -> sees linked students and fee statements.
4. Admin monitors pipeline and account health through dashboard metrics.

### Integration points
- Payment gateway APIs (M-Pesa Daraja first).
- Notification providers (email/SMS/WhatsApp).
- Optional BI exports (CSV/API).

---

## 4) Roadmap and phased milestones

## Phase 0 — Discovery & alignment (Week 1)
- Finalize MVP scope and non-functional requirements.
- Establish KPI baseline and analytics events.
- Deliverables: discovery brief, IA draft, risk register, sprint backlog.

## Phase 1 — MVP build (Weeks 2–6)
- Build navigation shell, auth hardening, demo request journey, fee statement UX.
- Create token set + 10 foundational components.
- Deliverables: production-ready MVP + smoke tests + staging runbook.

## Phase 2 — Stabilization and scale foundation (Weeks 7–12)
- Add role dashboards, advanced filtering, async notifications, and observability.
- Introduce API contracts (OpenAPI) and improve automated test depth.
- Deliverables: SLO dashboards, alerting, E2E critical path coverage.

## Phase 3 — Enhancements (Quarter 2+)
- Self-serve onboarding wizard, richer analytics, localization, experimentation (A/B).
- Deliverables: feature flags, growth experiment cadence, scaling validation.

---

## 5) Risk assessment and mitigation

| Risk | Impact | Mitigation | Fallback |
|---|---|---|---|
| Scope creep in MVP | Delays launch | Strict MVP gate and change-control process | Shift non-critical features to Phase 2 |
| Team skill gaps in modern frontend | Quality/velocity risk | Pairing, templates, coding playbooks | Hybrid migration while retaining PHP views |
| Inconsistent API behavior | Integration friction | OpenAPI contract-first + versioning | API facade and compatibility layer |
| Accessibility debt | Legal and UX risk | WCAG checks in CI + design review gates | Accessibility remediation sprint |
| Weak observability | Slow incident response | Structured logs + tracing + alerts from day one | Operational runbook + manual checks |

---

## 6) Starter project setup

A minimal scaffold has been added under `starter-skeleton/` to accelerate implementation planning.

### Repository structure (proposed)
```text
starter-skeleton/
  apps/
    web/
      src/{app,components,features,lib,styles}
    api/
      src/{modules,shared}
  packages/{ui,tokens,config,types}
  infra/{docker,terraform,monitoring}
  tests/{e2e,integration,performance}
  .github/workflows/ci-template.yml
  docs/ARCHITECTURE_DECISIONS.md
  README.md
```

### Essential scripts (to implement in chosen toolchain)
- `dev:web`, `dev:api`
- `lint`, `test`, `test:e2e`
- `build`, `deploy:staging`

### Minimal skeleton policy
- Files/folders only for now.
- Add framework-specific bootstrapping after stack and budget confirmation.

---

## 7) Initial coding standards and governance

### Style guide (baseline)
- TypeScript strict mode for frontend.
- ESLint + Prettier for formatting/linting.
- Conventional commits.
- Branch naming: `feat/*`, `fix/*`, `chore/*`, `docs/*`.

### PR process
- PR template required: problem, scope, screenshots, risk, rollback.
- Required checks: lint, tests, accessibility scan, performance budget check.
- At least 1 reviewer approval; 2 for auth/payment-touching changes.

### Testing approach
- Unit tests for domain logic.
- Integration tests for APIs and DB interactions.
- E2E tests for critical user journeys (login, fee view, approval flow).
- Contract tests for external integrations.

---

## 8) Key next steps for discovery and design review

1. Hold 90-minute stakeholder workshop (product, design, backend, ops).
2. Validate user journeys with 5–8 target users (parent/admin/institution rep).
3. Freeze MVP acceptance criteria and prioritize backlog.
4. Approve design token baseline and component inventory.
5. Sign off architecture decision records (frontend path, backend strategy, hosting).
6. Launch Phase 1 with weekly KPI review and risk log updates.

---

## Required constraints to finalize implementation plan
Please confirm:
1. Preferred stack direction (remain PHP-first vs hybrid Next.js migration).
2. MVP deadline (6-week vs 12-week target).
3. Team capacity (engineers, QA, design, DevOps availability).
4. Budget limits and hosting/provider constraints.
5. Compliance/security obligations (financial data, audit, residency).
6. Branding and localization requirements.
