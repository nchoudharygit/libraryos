# LibraryOS — Library Management System

A microservices-based library management system built with PHP/Laravel, Vue.js, Docker, and AWS.
This document is the single source of truth for the project — what it is, why it's built this way, and how to work on it.

---

## What is this project?

LibraryOS is a system to manage a real library — books, authors, members, and borrowing.
Think of it like a city library's internal software.

It is built as **microservices** — instead of one big application, it is split into small, independent services that each own one part of the domain and talk to each other via REST APIs. A single **Gateway** sits in front of them and is the only entry point from the internet.

This project is built primarily for **interview depth and hands-on recall**, not as a public demo. Every architectural choice below is one that can be explained and defended in an interview.

---

## The real-world domain (what problem does this solve?)

A library needs to:

- Know what books it owns and who wrote them → **Catalog service**
- Know who its members are → **Consumer service**
- Track borrowing, returns, and overdue status → **Transaction service**
- Authenticate librarians and give them one place to work → **Gateway (Laravel dashboard)**
- Show stats to the librarian (most borrowed books, active members, overdue count) → computed inside the **Gateway**, not a separate service
- Show all of this in a web dashboard → **UI (Vue.js, served by the Gateway)**

---

## Services

### 1. Catalog Service (PHP/Laravel)
**Responsibility:** Manage books and authors.

What it stores:
- Authors (name, bio, nationality)
- Books (title, ISBN, genre, author, total copies, available copies)

API endpoints it exposes (internal only, not reachable from the internet directly):
- `GET /api/authors` — list all authors
- `POST /api/authors` — add an author
- `GET /api/books` — list all books
- `POST /api/books` — add a book
- `GET /api/books/{id}` — get one book's details, including availability

This is the **foundation** — Transaction service depends on book and author data from here.

Own database: `catalog_db`.

---

### 2. Consumer Service (PHP/Laravel)
**Responsibility:** Manage library members.

What it stores:
- Member profiles (name, email, membership ID, joined date)
- Membership status (active / suspended)

API endpoints (internal only):
- `GET /api/members` — list all members
- `POST /api/members` — register a new member
- `GET /api/members/{id}` — get one member's details, including active status

Own database: `consumer_db`.

---

### 3. Transaction Service (PHP/Laravel)
**Responsibility:** Track borrow/return transactions and overdue status.

What it stores:
- Borrow records (book_id, consumer_id, issue date, due date)
- Return records and overdue status

Before recording a transaction, it verifies with two other services:
- Calls **Catalog service** (`GET /api/books/{id}`) to confirm the book exists and is available
- Calls **Consumer service** (`GET /api/members/{id}`) to confirm the member is active

These calls use Laravel's `Http` client with a short timeout. If either service is unreachable or times out, the request fails cleanly with a `503` and no transaction record is created. If either service responds but says "not available" or "not active," the request fails with a `422`. No partial writes in either case.

API endpoints (internal only):
- `POST /api/transactions/borrow` — borrow a book
- `POST /api/transactions/return` — return a book
- `GET /api/transactions` — list all borrow/return records
- `GET /api/transactions/overdue` — overdue count and list

Own database: `transaction_db`.

---

### 4. Gateway (Laravel dashboard + Vue.js UI)
**Responsibility:** The single public entry point. Handles login, routes requests internally, and renders the dashboard.

This is the only service the Application Load Balancer forwards traffic to. Catalog, Consumer, and Transaction services are never reachable directly from the internet — only from the Gateway, enforced at the security group level.

What it does:
- Google login via Auth0 — verifies every incoming request before anything else happens
- After login, calls Catalog / Consumer / Transaction services internally over service discovery (private DNS, not the public internet)
- Aggregates data from all three services to compute dashboard analytics:
  - Most borrowed books
  - Active vs inactive member counts
  - Overdue count
  - Borrow trends
- Serves the Vue.js frontend (Catalog, Members, Transactions, Analytics tabs)
- Shows ALB / backend health check status on the dashboard

There is no separate Analytics service — analytics is a read-only aggregation done inside the Gateway by calling the three backend services, so it does not need its own database.

---

## How services talk to each other

Backend services (Catalog, Consumer, Transaction) **never share a database**. Each owns its data; others must ask for it via HTTP.

The Gateway is the only service exposed to the internet. Everything else is reached only through it.

Example flow — a member borrows a book:

```
Browser → ALB → Gateway (Auth0 login check)
                    ↓
Gateway → POST /api/transactions/borrow (Transaction service, via service discovery)
                    ↓
Transaction → GET /api/books/{id} (Catalog service) → verify book exists and is available
                    ↓
Transaction → GET /api/members/{id} (Consumer service) → verify member is active
                    ↓
Transaction → record borrow → return result to Gateway → Gateway returns result to browser
```

Two microservice principles at work here:
1. **Each service owns its data, others must ask.**
2. **Nothing is reachable except through the Gateway** — this is the API Gateway / Backend-for-Frontend pattern, and it is enforced by network rules (security groups), not just by convention.

---

## Tech stack

| Layer | Technology |
|---|---|
| Backend services | PHP 8.2 + Laravel |
| Gateway + Frontend | Laravel + Vue.js 3 |
| Database | PostgreSQL — single RDS instance, one database per service (`catalog_db`, `consumer_db`, `transaction_db`) |
| Containerization | Docker + docker-compose |
| Container registry | Amazon ECR |
| Compute | Amazon ECS + Fargate |
| Load balancer | Application Load Balancer (ALB) — single target group, points only to the Gateway |
| Service-to-service discovery | AWS Cloud Map (ECS Service Discovery) |
| Infrastructure | Terraform |
| CI/CD | GitHub Actions |
| Secrets | AWS Secrets Manager |
| Logging | Amazon CloudWatch Logs |
| Auth | Auth0 (Google login) |

---

## Project folder structure

```
libraryos/
├── services/
│   ├── catalog-service/        # Laravel app — Books & Authors
│   ├── consumer-service/       # Laravel app — Members
│   └── transaction-service/    # Laravel app — Borrow/Return
├── gateway/                     # Laravel + Vue.js — login, routing, dashboard, analytics
├── infra/                       # Terraform files
│   ├── vpc.tf
│   ├── ecs.tf
│   ├── rds.tf
│   ├── alb.tf
│   ├── service-discovery.tf
│   └── secrets.tf
├── .github/
│   └── workflows/
│       └── deploy.yml           # CI/CD pipeline
└── docker-compose.yml            # Local development
```

---

## Local development setup

All services run together using docker-compose.

```bash
# Clone the repo
git clone https://github.com/nchoudharygit/libraryos.git
cd libraryos

# Start everything
docker compose up --build

# Services will be available at:
# Gateway (UI + API):     http://localhost:8000
# Catalog service:        http://localhost:8081/api  (internal — for local testing only)
# Consumer service:       http://localhost:8082/api  (internal — for local testing only)
# Transaction service:    http://localhost:8083/api  (internal — for local testing only)
# PostgreSQL:              localhost:5433
```

Locally, the backend service ports are exposed for testing convenience. In AWS, they are not — only the Gateway is reachable from outside the VPC.

---

## Dockerfile strategy (per service)

Each service uses a **multistage Dockerfile**:

- **Stage 1 (builder):** Full PHP + Composer image — installs all dependencies
- **Stage 2 (production):** Slim `php:8.2-fpm-alpine` — only copies app code + vendor folder
- Runs as a **non-root user** (`www-data`) for security
- No dev dependencies, no build tools in the final image

This keeps the production image small and secure.

---

## CI/CD Pipeline

Every push to `main` triggers:

```
Code push
    → Lint + PHPUnit tests
    → Docker build (multistage)
    → Push image to ECR (tagged with git SHA)
    → Run DB migrations (one-off ECS task, per service)
    → Deploy updated task definition to ECS (rolling update)
```

Authentication to AWS uses **OIDC** — no long-lived AWS keys stored in GitHub secrets.

---

## AWS Infrastructure

### VPC layout
```
Custom VPC
├── Public subnets (2 AZs)
│   └── Application Load Balancer (single target group → Gateway only)
└── Private subnets (2 AZs)
    ├── ECS Fargate tasks — Gateway, Catalog, Consumer, Transaction
    └── RDS PostgreSQL (single instance, 3 databases)
```

### Traffic rules (strict)
- Internet → ALB only (port 443)
- ALB → Gateway only (port 80) — this is the only backend target group
- Gateway → Catalog / Consumer / Transaction, via service discovery only
- Catalog / Consumer / Transaction → RDS only (port 5432)
- Catalog / Consumer / Transaction security groups accept traffic **only from the Gateway's security group** — not from the ALB, not from the internet
- No direct internet access to any backend service or RDS

### Auto Scaling
- Scale out when CPU ≥ 70% or Memory ≥ 80%
- Also scales on ALB request count (Gateway only, since it's the only ALB target)
- Min tasks: 1 per service, Max: 10 per service

### Cost control
- `terraform destroy` script kept ready and tested from day one
- Infrastructure spun up only when actively working or demoing, destroyed after — RDS, Fargate, and ALB running 24/7 are not left on

---

## Build order (development phases)

### Phase 0 — Foundation (already done)
- [x] Core PHP CRUD (separate portfolio piece, not part of this microservices build)
- [x] Laravel dashboard with Vue.js UI and Auth0 Google login
- [x] Docker (dev-friendly baseline)
- [x] PostgreSQL running locally

### Phase 1 — Split into 3 services (local only)
- [ ] Catalog service (Laravel — Books + Authors)
- [ ] Consumer service (Laravel — Members)
- [ ] Transaction service (Laravel — Borrow/Return, calls Catalog + Consumer)
- [ ] Error handling for failed cross-service calls
- [ ] docker-compose local setup, all 3 services + Postgres (3 databases)
- [ ] End-to-end local test: happy path and failure path

### Phase 2 — Production-ready Docker
- [ ] Multistage Dockerfiles for all services, non-root user
- [ ] Health check endpoint (`/health`) per service
- [ ] Unit tests per service

### Phase 3 — AWS networking foundation
- [ ] Terraform: custom VPC, public + private subnets across 2 AZs
- [ ] Security groups — strict traffic rules as above
- [ ] RDS PostgreSQL, 3 databases
- [ ] Secrets Manager for DB credentials

### Phase 4 — ECS Fargate + Gateway
- [ ] ECR repos, ECS cluster, task definitions for all 4 services
- [ ] AWS Cloud Map / ECS Service Discovery
- [ ] ALB with a single target group pointing to the Gateway
- [ ] Gateway auth middleware + internal routing to backend services

### Phase 5 — CI/CD pipeline
- [ ] GitHub Actions: lint, test, build, push to ECR, deploy to ECS
- [ ] DB migrations run as a pipeline step
- [ ] Tests gate the deploy step

### Phase 6 — Observability and scaling
- [ ] CloudWatch Logs per service
- [ ] ECS Service Auto Scaling
- [ ] Analytics tab in the Gateway dashboard, 3–4 concrete stats

---

## Key concepts to remember

**Why microservices?**
Each service can be deployed, scaled, and updated independently. If Transaction service has a bug, only that one is redeployed — Catalog and Consumer keep running.

**Why a Gateway instead of routing the ALB directly to each service?**
Security and a single point of control for authentication. No backend service is ever reachable except through the Gateway, enforced by network rules, not just convention. Trade-off: the Gateway becomes a dependency every request goes through, so it needs to be reliable and scaled properly.

**Why Fargate?**
Serverless containers — no EC2 instances to manage. AWS handles the underlying servers. Just define CPU/memory and the Docker image.

**Why Terraform?**
Infrastructure as code — the entire AWS setup is reproducible and version-controlled. One command creates everything, one command destroys it.

**Why Secrets Manager?**
Database passwords and OAuth client secrets are never hardcoded or stored in environment files in the repo. ECS tasks pull them at runtime via IAM role.

**Why multistage Docker?**
Smaller image = faster deployment, smaller attack surface. Dev dependencies (Xdebug, test libraries) never make it to production.

**Why one database per service instead of one shared database?**
Each service owns its data completely — no other service can read or write it directly, which is what makes them independently deployable. The trade-off is no cross-service SQL joins and no built-in transactional consistency across services, which is why the Transaction service verifies data via HTTP calls instead of a join, and why a failed check never leaves a partial record behind.

---

*GitHub: github.com/nchoudharygit/libraryos*
*Stack: PHP/Laravel · Vue.js · Docker · PostgreSQL · AWS ECS Fargate · Terraform*
