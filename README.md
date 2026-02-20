# 🚀 **LaraNexus: The Ultimate FREE Laravel SaaS Deep-Dive (2026 Edition)**

**LaraNexus** is no longer just a starter kit. It is a **comprehensive, production-grade curriculum** designed to take
you from "Junior Developer" to "Laravel System Architect." This repository is a **Dockerized Monorepo** containing a
fully functional SaaS application specifically architected to teach you the hardest, highest-paying concepts in modern
backend development—for **$0 cost**.

**The Mission:** Build a Multi-Tenant SaaS platform capable of handling enterprise load, complex database topologies,
and real-time automation, using only free tools and community resources.

**Support the Project:** ⭐ Star on GitHub | 🐛 Open an Issue | 👨‍💻 Contribute a Module

---

## 🧠 **The "Why" (Architectural Philosophy)**

Most tutorials teach you *how* to make things work. LaraNexus teaches you *how things break* and how to prevent it.

* **Multi-Tenancy:** Not just separate databases, but isolated execution contexts.
* **Separation of Concerns:** We enforce Action Classes, DTOs, and View Models to keep logic testable.
* **Infrastructure as Code:** Learn why your local environment should mirror production exactly via Docker.

---

## 🎓 **The "Semester" Roadmap (Free Curriculum)**

This project is structured into 4 intense modules. Completing all modules guarantees a portfolio-ready skillset.

### **Module 1: Infrastructure & Container Orchestration**

*Goal: Master the environment where your code lives.*

| **Topic**             | **Deep Dive Challenge**                                                                                                                            | **Free Resource**                                                                          |
|:----------------------|:---------------------------------------------------------------------------------------------------------------------------------------------------|:-------------------------------------------------------------------------------------------|
| **Docker Internals**  | Configure a multi-stage build to reduce image size by 60%. Debug a running container without installing editors inside the image.                  | [Docker Curriculum](https://docker-curriculum.com/)                                        |
| **Traefik Proxy**     | Replace standard Nginx with Traefik for automatic SSL generation (local simulation) and load balancing between multiple app containers.            | [Traefik Docs](https://doc.traefik.io/traefik/)                                            |
| **Database Topology** | Configure Read/Write splitting in Laravel. Send heavy analytics queries to the PostgreSQL replica while keeping MySQL as the transactional master. | [Laravel DB Read/Write](https://laravel.com/docs/11.x/database#read-and-write-connections) |

### **Module 2: Advanced Eloquent & Data Engineering**

*Goal: Treat your database as an asset, not a dumping ground.*

| **Topic**                 | **Deep Dive Challenge**                                                                                                    | **Free Resource**                                                                                                     |
|:--------------------------|:---------------------------------------------------------------------------------------------------------------------------|:----------------------------------------------------------------------------------------------------------------------|
| **Polymorphism**          | Implement a "Universal Search" using Polymorphic relations where `Task`, `Project`, and `Invoice` can all be "Searchable." | [Laravel Docs: Polymorphic Relations](https://laravel.com/docs/11.x/eloquent-relationships#polymorphic-relationships) |
| **Indexing Theory**       | Use `EXPLAIN` to analyze a slow query. Create a composite index that reduces query time from 800ms to <10ms.               | [Use The Index, Luke!](https://use-the-index-luke.com/) (Highly Recommended)                                          |
| **Database Transactions** | Implement "Double-Entry Ledger" accounting logic using `DB::transaction()` and nested transactions with savepoints.        | [Laravel Docs: Transactions](https://laravel.com/docs/11.x/database#database-transactions)                            |

### **Module 3: Asynchronous Architecture (The Queue)**

*Goal: Decouple execution time from user response time.*

| **Topic**           | **Deep Dive Challenge**                                                                                                           | **Free Resource**                                                                                                        |
|:--------------------|:----------------------------------------------------------------------------------------------------------------------------------|:-------------------------------------------------------------------------------------------------------------------------|
| **Redis & Horizon** | Install Laravel Horizon. Create a "Retry Until Success" logic for external API calls with exponential backoff.                    | [Laravel Horizon Docs](https://laravel.com/docs/11.x/horizon)                                                            |
| **Job Batching**    | Process a CSV export of 50,000 users. Use Job Batching to show a real-time progress bar to the user without blocking the browser. | [Laravel Docs: Batching](https://laravel.com/docs/11.x/queues#job-batching)                                              |
| **Idempotency**     | Design a queue worker that can safely be restarted without sending duplicate emails (Idempotency Keys).                           | [Martin Fowler: Idempotency](https://martinfowler.com/articles/patterns-of-distributed-systems/idempotent-receiver.html) |

### **Module 4: Event-Driven Automation**

*Goal: Create a system that reacts intelligently to user behavior.*

| **Topic**               | **Deep Dive Challenge**                                                                                                                            | **Free Resource**                                                   |
|:------------------------|:---------------------------------------------------------------------------------------------------------------------------------------------------|:--------------------------------------------------------------------|
| **Twilio Webhooks**     | Build a "Conversation Bot" that tracks context. If a user replies "YES" to an appointment, update the DB and trigger a follow-up "What time?" SMS. | [Twilio Webhooks Guide](https://www.twilio.com/docs/usage/webhooks) |
| **Novu Workflows**      | Create a "Digest" workflow. Instead of 10 emails for 10 comments, send 1 summary email every 30 minutes using Novu's digest engine.                | [Novu Digest Docs](https://docs.novu.co/platform/digest)            |
| **Reverb (WebSockets)** | Implement a "Live Cursor" feature where you can see other users' mouse cursors moving on the dashboard in real-time.                               | [Laravel Reverb Docs](https://laravel.com/docs/11.x/reverb)         |

---

## 🏗️ **Tech Stack (The "2026" Standard)**

We use bleeding-edge tech that modern enterprises demand.

```text
🖥️  Backend Core:
    ├── PHP 8.3 (JIT Compiler enabled)
    ├── Laravel 11 (Slim skeleton, PHP native attributes)
    └── FrankenPHP (Modern app server built on Caddy/Go - optional)

🎨  Frontend:
    ├── Vue 3 (Composition API)
    ├── Inertia.js (The glue)
    ├── Tailwind CSS v4 (Oxidizer engine)
    └── Ziggy (Named routes in JS)

🗄️  Data Layer:
    ├── MySQL 9 (Transactional data)
    ├── PostgreSQL 16 (Geospatial & Analytics)
    ├── Redis Stack (Caching + Queues + JSON search)
    └── Meilisearch (Full-text search)

⚙️  Infrastructure:
    ├── Docker Compose (Development)
    ├── Traefik (Load Balancing/SSL)
    └── GitHub Actions (CI/CD Pipeline)
```

---

## 🚀 **"Zero to Hero" Quick Start**

### **Prerequisites**

1. **Docker Desktop** (Allocated 8GB RAM min).
2. **VS Code** + Dev Containers Extension (Recommended).
3. **TablePlus** or **DBeaver** (Community Edition).

### **1. Initial Clone & Configuration**
```bash
git clone https://github.com/traxim-tech/laranexus.git
cd laranexus

# Setup environment (Copy the example)
cp .env.example .env

# Fix permissions for Linux/WSL users
chmod -R 775 storage bootstrap/cache
```

### **2. The Launch Sequence**
```bash
# Build the containers (Grab a coffee, first time takes ~3 mins)
docker compose up -d --build

# Install dependencies
docker compose exec app composer install
docker compose exec app npm install

# Secure the app
docker compose exec app php artisan key:generate

# Initialize the Multi-Tenant DB Structure
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan tenants:migrate

# Build the frontend assets
docker compose exec app npm run build
```

### **3. Access Points**

| Service           | URL                     | Purpose                     |
|:------------------|:------------------------|:----------------------------|
| **Main App**      | `http://localhost`      | The SaaS Dashboard          |
| **Mailpit**       | `http://localhost:8025` | Email testing UI            |
| **Redis Insight** | `http://localhost:8001` | Visualize your queues/keys  |
| **Minio**         | `http://localhost:9000` | S3 Compatible Local Storage |

---

## 🧪 **The "SaaS Boss Battles" (Coding Challenges)**

Once the app is running, try to implement these features. If you get stuck, check the `solutions/` branch in the repo.

### **⚔️ Boss Battle 1: The N+1 Slayer**

**Scenario:** The dashboard loads 50 projects, each showing the owner and their latest comment.
**Problem:** The current code creates 150 SQL queries (N+1 problem).
**Challenge:** Use Laravel Debugbar to identify the queries. Refactor the Controller to use `with()` eager loading.
Reduce queries to < 5.
**Learn:** Eloquent Optimization.

### **⚔️ Boss Battle 2: The Race Condition**

**Scenario:** Two admins try to assign the same "Premium User" license at the exact same millisecond.
**Problem:** The system allows 2 licenses to be used when only 1 exists.
**Challenge:** Implement **Database Locking** (`lockForUpdate`) or **Redis Atomic Locks** to ensure only one assignment
succeeds.
**Learn:** Concurrency handling.

### **⚔️ Boss Battle 3: The Multi-DB Switcher**

**Scenario:** The system needs to generate a global report combining data from *all* tenants.
**Challenge:** Use the `tenancy` package to loop through databases, execute a query, and aggregate the results into the
central `system` database without hardcoding connection strings.
**Learn:** Multi-tenancy architecture.

---

## 📂 **Directory Structure (Domain-Driven)**

We don't just dump files in `app/Http/Controllers`. We organize by **Domain**.

```text
app/
├── Domains/
│   ├── Appointment/
│   │   ├── Actions/         # Single-responsibility classes
│   │   ├── DataObjects/     # DTOs for type safety
│   │   └── Listeners/       # Event handlers
│   ├── Notification/
│   │   └── Channels/        # Custom Novu channel
│   └── Billing/
│       └── Services/        # Gateway logic
├── Infrastructure/
│   ├── Scopes/              # Global query scopes
│   └── Policies/            # Authorization logic
└── Support/
    └── Helpers/             # Global helper functions
```

---

## 🛠️ **Advanced Developer Workflow**

### **Tinker CLI on Steroids**
```bash
# Enter the app container
docker compose exec app bash

# Test a heavy calculation without hitting the browser
php artisan tinker
>>> App\Domains\Appointment\Actions\CalculateSlots::run('2026-01-01');
```

### **Queue Simulation**

Simulate a high-traffic event locally.

```bash
# Open 3 terminals and run:
docker compose exec app php artisan queue:work --queue=high,default,low

# In a 4th terminal, trigger 500 jobs:
php artisan benchmark:jobs --count=500
```

---

## 📊 **Performance Optimization Lab**

This project includes a built-in performance testing script.

```bash
# Run the benchmark suite
docker compose exec app php artisan benchmark:run
```

**Your Goal:** Beat these baseline numbers on your hardware.

* **Route Caching:** < 20ms
* **View Caching:** < 15ms
* **Optimized Query (1000 rows):** < 50ms

---

## 📚 **The "Infinite" Resource List (100% Free)**

To truly master this stack, we have curated the best free content on the internet.

### **Deep Dive: Laravel Architecture**

* **[Laravel Docs 11.x](https://laravel.com/docs/11.x):** The source of truth. Read the "Architecture" section
  specifically.
* **[Spatie's Guidelines](https://spatie.be/guidelines):** A free look at how the top Laravel agency structures their
  code.
* **[FreeCodeCamp Laravel Course](https://www.youtube.com/watch?v=MFPPXttXnws):** 4-hour crash course.

### **Deep Dive: Database & SQL**

* **[Use The Index, Luke!](https://use-the-index-luke.com/):** Free book on SQL indexing. Critical for SaaS.
* **[PostgreSQL Exercises](https://pgexercises.com/):** Interactive SQL challenges.

### **Deep Dive: Docker & DevOps**

* **[Docker for Developers (YouTube)](https://www.youtube.com/watch?v=Q6UwuWs6RJI):** Free course on containerization
  concepts.

---

## 👥 **The Upwork "Ace" Proposal Template**

Completing this project gives you tangible proof of skills. Use this template:

> **Subject:** Laravel 11 Multi-Tenant SaaS Specialist - Proven Portfolio
>
> Hi [Client Name],
>
> I noticed you are looking for a robust backend solution. I specialize in **Enterprise Laravel Architecture**,
> specifically focusing on performance and scalability—skills I honed building **LaraNexus** (an open-source SaaS
> boilerplate).
>
> **What I bring to your project:**
> * **Optimization:** Experience reducing page load times by 60% via Redis caching and query indexing.
> * **Automation:** Integration of 2-way SMS systems (Twilio) and notification workflows (Novu).
> * **Architecture:** Ability to set up isolated multi-tenant environments for data security.
>
> You can review the code structure here: [GitHub Link]
>
> I am ready to apply this "production-first" mindset to your project immediately.
>
> Best,
> [Your Name]

---

## 📈 **Future Roadmap (Contribution Opportunities)**

We are looking for contributors to build out these modules. This is your chance to add "Open Source Contributor" to your
resume.

*   [ ] **Module: Filament PHP Integration** (Admin Panel)
*   [ ] **Module: OpenAI Chat Integration** (RAG pipeline)
*   [ ] **Module: PWA Offline Mode** (Service Workers)
*   [ ] **Module: Stripe Billing Integration** (Webhooks handling)

**Ready to become a Senior Developer? Clone the repo and start building. 🚀**