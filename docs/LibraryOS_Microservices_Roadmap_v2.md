# LibraryOS — Microservices on AWS Fargate: Roadmap

**Goal:** Interview prep / hands-on depth, not a public-facing demo. Priority = understanding over polish.

**Final architecture:**
- 3 backend services: Catalog (Books + Authors), Consumer (Members), Transaction (Issue/Return) — **never directly exposed to internet**
- **Laravel dashboard = Gateway/BFF** — handles Auth0 login, and after login internally calls Catalog/Consumer/Transaction via service discovery
- Transaction service also calls Catalog + Consumer internally via ECS Service Discovery (sync calls, no async/Kafka)
- 1 RDS PostgreSQL instance, 3 separate databases: `catalog_db`, `consumer_db`, `transaction_db`
- 1 VPC, public subnet (ALB) + private subnets (ECS tasks + RDS)
- ALB has **1 target group only** (the Gateway/Laravel service) — no path-based routing to backend services from ALB anymore

**Traffic flow:** `Internet → ALB (TLS, health check) → Gateway (Laravel, Auth0 login check) → Catalog / Consumer / Transaction (private, service discovery only)`

---

## Phase 0 — Foundation check (what's already done)
- [x] Core PHP `Book.php` CRUD (PDO) — becomes base for Catalog service
- [x] Laravel version with Vue.js UI, Auth0 Google login — becomes the frontend / dashboard
- [x] Docker (dev-friendly, not production-hardened yet)
- [x] PostgreSQL running locally
- [x] AIService.php (GenAI bonus) — keep, extra talking point

**Action:** Decide which codebase (core PHP or Laravel) becomes Catalog service base. Recommendation: core PHP (lighter, easier to split into microservice) — Laravel stays as the unified dashboard/BFF that calls all 3 services.

---

## Phase 1 — Split into 3 services (local only, no AWS yet)
1. Extract Catalog service (Books + Authors) from existing code → own repo folder, own `Dockerfile`, own DB connection (`catalog_db`)
2. Build Consumer service (Members CRUD) — new, simple REST API
3. Build Transaction service (Issue/Return) — new, calls Catalog + Consumer over HTTP (use plain URLs for now, e.g. `http://catalog-service:8080`)
4. `docker-compose.yml` with all 3 services + 1 Postgres container (3 databases inside it) + Laravel dashboard — test full flow locally first

**Definition of done:** Can issue a book to a member locally, using 3 separate containers talking to each other.

---

## Phase 2 — Production-ready Docker
1. Multi-stage Dockerfile per service (small image, non-root user)
2. Basic health check endpoint per service (`/health`) — needed later for ALB target groups
3. Write/adapt unit tests per service (at least core CRUD + the Transaction cross-service call)

**Definition of done:** Each service has a lean, non-root production image; tests pass locally.

---

## Phase 3 — AWS networking foundation
1. Custom VPC — 1 public subnet (ALB), 2+ private subnets (ECS tasks, RDS) across 2 AZs for RDS requirement
2. Security groups: Internet→ALB (80/443 only), ALB→ECS (app port only), ECS→RDS (5432 only) — nothing else allowed
3. RDS PostgreSQL instance in private subnet, 3 databases created inside it
4. Secrets Manager — DB credentials per service, service pulls at container start

**Definition of done:** VPC + RDS provisioned via Terraform (reuse Notely's Terraform patterns), no ECS yet — just confirm connectivity/security groups are correct.

---

## Phase 4 — ECS Fargate + service discovery + Gateway
1. ECR repos — one per service (catalog, consumer, transaction) + Laravel dashboard (Gateway)
2. ECS cluster, 4 Fargate task definitions (3 backend services + Gateway)
3. AWS Cloud Map / ECS Service Discovery — so Gateway and Transaction service can resolve `catalog.local`, `consumer.local`, `transaction.local` internally
4. **ALB has 1 target group only — the Gateway (Laravel).** No path-based routing to backend services; ALB just forwards everything to Gateway after TLS + health check
5. Gateway middleware: verify Auth0 token first, then internally call the right backend service based on the request
6. Backend services (Catalog/Consumer/Transaction) get security group rules that **only allow traffic from the Gateway's security group** — not from ALB, not from internet

**Definition of done:** Hit the ALB URL → Gateway checks login → if valid, Gateway calls Catalog/Consumer/Transaction internally and returns combined response. Trying to hit a backend service's IP directly (bypassing Gateway) should fail — security group blocks it.

---

## Phase 5 — Pipeline (CI/CD)
1. GitHub Actions — reuse Notely's 3-workflow pattern (ci.yml, deploy.yml, infra.yml), adapt for 4 services
2. Pipeline: Code push → Lint & test → Build Docker image → Push to ECR → Deploy to ECS (per service)
3. DB migrations run as a pipeline step (or one-off ECS task) before deploy
4. Unit tests gate the deploy step

**Definition of done:** Push to main → pipeline builds, tests, and deploys all affected services automatically.

---

## Phase 6 — Observability + scaling
1. CloudWatch Logs — each service's container logs routed and viewable
2. ECS Service Auto Scaling — CPU/memory based (start simple, request-count based is a stretch goal)
3. Basic analytics on Laravel dashboard — decide 3-4 concrete stats (e.g. books issued this month, active members, overdue books, most-borrowed book) — avoid vague scope

**Definition of done:** Can watch logs live in CloudWatch, trigger a load test and see auto scaling kick in, dashboard shows real numbers pulled from the 3 services.

---

## Cost control (important — no income right now)
- Keep `terraform destroy` script ready and tested from day 1
- Spin up only when actively working/demoing, destroy after
- RDS + Fargate + ALB running 24/7 will burn money — don't leave it up overnight

## Explicitly out of scope (mention as "future enhancement" in interviews, don't build)
- Facebook login (Google via Auth0 is enough)
- Async/event-driven communication (Kafka/SQS) between services
- DB-per-service on separate RDS instances (using 1 instance, 3 databases instead — still defensible in interview)

---

## Suggested order to actually work in
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5 → Phase 6.
Don't skip ahead to AWS before Phase 1–2 are solid locally — debugging 3-service networking issues is much harder once AWS variables (security groups, IAM, DNS) are added on top.
