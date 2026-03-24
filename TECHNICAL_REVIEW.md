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
