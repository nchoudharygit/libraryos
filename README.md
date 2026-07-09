# LibraryOS — Library Management System

A microservices-based library management system built with PHP/Laravel, Vue.js, Docker, and AWS.  
This document is the single source of truth for the project — what it is, why it's built this way, and how to work on it.

---

## What is this project?

LibraryOS is a system to manage a real library — books, authors, members, and borrowing.  
Think of it like a city library's internal software.

It is built as **microservices** — meaning instead of one big application, it is split into small, independent services that each do one thing well and talk to each other via REST APIs.

---

## The real-world domain (what problem does this solve?)

A library needs to:

- Know what books it owns and who wrote them → **Catalog service**
- Track which books are available, borrowed, or overdue → **Inventory service**
- Know who its members are → **Consumer service**
- Show stats to the librarian (most borrowed books, active members, overdue count) → **Analytics service**
- Show all of this in a web dashboard → **UI (Vue.js)**

---

## Services

### 1. Catalog Service (PHP/Laravel)
**Responsibility:** Manage books and authors.

What it stores:
- Authors (name, bio, nationality)
- Books (title, ISBN, genre, author, number of copies owned)

API endpoints it exposes:
- `GET /api/authors` — list all authors
- `POST /api/authors` — add an author
- `GET /api/books` — list all books
- `POST /api/books` — add a book
- `GET /api/books/{id}` — get one book's details

This is the **foundation** — every other service depends on book and author data from here.

---

### 2. Inventory Service (PHP/Laravel)
**Responsibility:** Track stock levels and borrow/return transactions.

What it stores:
- How many copies of each book are currently available
- Borrow records (who borrowed what, when, due date)
- Return records and overdue status

It calls the **Catalog service** to verify a book exists before recording a transaction.  
It calls the **Consumer service** to verify a member is valid before allowing a borrow.

API endpoints:
- `POST /api/borrow` — borrow a book
- `POST /api/return` — return a book
- `GET /api/transactions` — list all borrow/return records
- `GET /api/inventory/{bookId}` — check stock for a book

---

### 3. Consumer Service (PHP/Laravel)
**Responsibility:** Manage library members.

What it stores:
- Member profiles (name, email, membership ID, joined date)
- Membership status (active / suspended)

API endpoints:
- `GET /api/members` — list all members
- `POST /api/members` — register a new member
- `GET /api/members/{id}` — get one member's details

---

### 4. Analytics Service (PHP/Laravel)
**Responsibility:** Aggregate data for the dashboard. Read-only.

It calls other services and computes:
- Most borrowed books (top 10)
- Active vs inactive members
- Overdue count
- Borrow trends (weekly/monthly)
- ALB health check status

API endpoints:
- `GET /api/stats/overview` — summary numbers
- `GET /api/stats/top-books` — top 10 most borrowed
- `GET /api/stats/overdue` — overdue count and list

---

### 5. UI Service (Vue.js)
**Responsibility:** The web dashboard that librarians use.

Features:
- Social login (Google OAuth)
- Tabs: Catalog, Inventory, Members, Analytics
- Each tab talks to its respective backend service
- Analytics tab shows charts and stats
- ALB health check status indicator

---

## How services talk to each other

Services **never share a database**. They only talk via HTTP REST calls.

Example flow — a member borrows a book:

```
UI → POST /api/borrow (Inventory service)
         ↓
Inventory → GET /api/books/{id} (Catalog service) → verify book exists
         ↓
Inventory → GET /api/members/{id} (Consumer service) → verify member is valid
         ↓
Inventory → check stock → record transaction → return success to UI
```

This is the key microservice principle: **each service owns its data, others must ask**.

---

## Tech stack

| Layer | Technology |
|---|---|
| Backend services | PHP 8.2 + Laravel 11 |
| Frontend | Vue.js 3 + Vite |
| Database | PostgreSQL (one DB per service) |
| Containerization | Docker + docker-compose |
| Container registry | Amazon ECR |
| Compute | Amazon ECS + Fargate |
| Load balancer | Application Load Balancer (ALB) |
| Infrastructure | Terraform |
| CI/CD | GitHub Actions |
| Secrets | AWS Secrets Manager |
| Logging | Amazon CloudWatch Logs |
| Auth | Google OAuth 2.0 (social login) |

---

## Project folder structure

```
libraryos/
├── services/
│   ├── catalog-service/        # Laravel app — Books & Authors
│   ├── inventory-service/      # Laravel app — Borrow/Return
│   ├── consumer-service/       # Laravel app — Members
│   └── analytics-service/      # Laravel app — Stats
├── ui/                         # Vue.js frontend
├── infra/                      # Terraform files
│   ├── vpc.tf
│   ├── ecs.tf
│   ├── rds.tf
│   ├── alb.tf
│   └── secrets.tf
├── .github/
│   └── workflows/
│       └── deploy.yml          # CI/CD pipeline
└── docker-compose.yml          # Local development
```

---

## Local development setup

All services run together using docker-compose.

```bash
# Clone the repo
git clone https://github.com/cneha1904/libraryos.git
cd libraryos

# Start everything
docker compose up --build

# Services will be available at:
# UI:                   http://localhost:5173
# Catalog service:      http://localhost:8001/api
# Inventory service:    http://localhost:8002/api
# Consumer service:     http://localhost:8003/api
# Analytics service:    http://localhost:8004/api
# PostgreSQL:           localhost:5432
```

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
    → Run DB migrations (one-off ECS task)
    → Deploy updated task definition to ECS (rolling update)
```

Authentication to AWS uses **OIDC** — no long-lived AWS keys stored in GitHub secrets.

---

## AWS Infrastructure

### VPC layout
```
Custom VPC
├── Public subnets (2 AZs)
│   └── Application Load Balancer
└── Private subnets (2 AZs)
    ├── ECS Fargate tasks (all 5 services)
    └── RDS PostgreSQL
```

### Traffic rules (strict)
- Internet → ALB only (port 443)
- ALB → ECS tasks only (port 80)
- ECS tasks → RDS only (port 5432)
- No direct internet access to ECS or RDS

### Auto Scaling
- Scale out when CPU ≥ 70% or Memory ≥ 80%
- Also scales on ALB request count
- Min tasks: 1 per service, Max: 10 per service

---

## Build order (development phases)

### Phase 1 — Development (current)
- [ ] Catalog service (Laravel REST API)
- [ ] Vue.js UI with Catalog tab
- [ ] docker-compose local setup

### Phase 2 — More services
- [ ] Inventory service
- [ ] Consumer service
- [ ] Analytics service
- [ ] Wire all tabs in UI

### Phase 3 — Containerization
- [ ] Multistage Dockerfiles for all services
- [ ] Optimized images, non-root user

### Phase 4 — Cloud
- [ ] Terraform: VPC, ECS, RDS, ALB, Secrets Manager
- [ ] GitHub Actions CI/CD pipeline
- [ ] CloudWatch logging and alarms

### Phase 5 — Advanced
- [ ] ECS Auto Scaling
- [ ] ALB health check dashboard in UI
- [ ] Google OAuth social login

---

## Key concepts to remember

**Why microservices?**  
Each service can be deployed, scaled, and updated independently. If Inventory service has a bug, you only redeploy that one — Catalog and Consumer keep running.

**Why Fargate?**  
Serverless containers — no EC2 instances to manage. AWS handles the underlying servers. You just define CPU/memory and your Docker image.

**Why Terraform?**  
Infrastructure as code — the entire AWS setup is reproducible and version-controlled. One command creates everything, one command destroys it.

**Why Secrets Manager?**  
Database passwords and OAuth client secrets are never hardcoded or stored in environment files in the repo. ECS tasks pull them at runtime via IAM role.

**Why multistage Docker?**  
Smaller image = faster deployment, smaller attack surface. Dev dependencies (Xdebug, test libraries) never make it to production.

---

*GitHub: github.com/cneha1904/libraryos*  
*Stack: PHP/Laravel · Vue.js · Docker · PostgreSQL · AWS ECS · Terraform*