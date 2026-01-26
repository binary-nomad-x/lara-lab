# 🚀 LaraNexus: The Ultimate Enterprise SaaS Boilerplate

**LaraNexus** is a high-performance, Dockerized Laravel ecosystem designed for building scalable real-world applications. This project serves as a "Mega-Lab" for experimenting with advanced software architecture, including Multi-Channel Notifications (Novu), Two-Way SMS Communication, and high-scale Background Processing.

---

## 🏗️ Core Architecture & Tech Stack

This project is built using a modern, decoupled stack to ensure maximum performance and scalability:

* **Backend:** Laravel 11 / PHP 8.3 (Optimized via PHP-FPM Alpine)
* **Frontend:** Vue.js 3 (Inertia.js) & Tailwind CSS
* **Infrastructure:** Docker & Docker Compose (Fully Isolated Environments)
* **Database:** MySQL 9.0 (Primary) & PostgreSQL (Analytics)
* **Caching/Queuing:** Redis (BullMQ logic compatible)
* **Notification Engine:** Novu (Email, SMS, Push, In-App)
* **Development Tools:** Mailpit (SMTP Testing), Meilisearch (Full-text search)

---

## 🛠️ Quick Start (Docker Environment)

Since this project is fully containerized, you do not need PHP, Composer, or Node.js installed on your host machine.

### 1. Clone & Environment Setup

```bash
git clone https://github.com/your-username/laranexus.git
cd laranexus
cp .env.example .env

```

### 2. Build and Launch

```powershell
docker-compose up -d --build

```

### 3. Initialize Application

```powershell
# Install PHP Dependencies
docker run --rm -v ${PWD}:/app composer install

# Run Migrations & Seeders
docker-compose exec app php artisan migrate --seed

# Generate App Key
docker-compose exec app php artisan key:generate

```

---

## 🌐 Advanced SaaS Features Integrated

### 1. Notification Infrastructure (Novu)

We use **Novu** to manage all user communications. Instead of hardcoding SMTP or Twilio logic, we trigger workflows:

* **Multi-Channel:** One trigger sends Email, SMS, and In-App notifications based on user preferences.
* **Delay Workflows:** Send a reminder SMS only if the user hasn't seen the In-App notification within 30 minutes.

### 2. Two-Way SMS System (Hospital/Appointment Logic)

A sophisticated bidirectional communication system:

* **Outbound:** Automation sends appointment reminders via Novu/Twilio.
* **Inbound (Webhooks):** A dedicated Node.js/Laravel webhook listens for patient replies (e.g., "YES", "NO").
* **Processing:** The system parses intents to automatically confirm or reschedule appointments in the database.

### 3. Heavy-Duty Background Processing

Utilizing **Redis Queues** to handle time-consuming tasks:

* Artisan commands for automated health checks.
* Job Batching for large-scale data exports.
* Real-time dashboard updates via WebSockets.

---

## 📊 Database Connectivity

For external GUI tools (DataGrip, TablePlus), use the following credentials:

| Service | Host | Port (Host) | Username | Password |
| --- | --- | --- | --- | --- |
| **MySQL** | `127.0.0.1` | `3306` | `root` | `root` |
| **Redis** | `127.0.0.1` | `6379` | `null` | `null` |
| **Mailpit** | `127.0.0.1` | `8025` | `(Web UI)` | `n/a` |

---

## 📁 Project Roadmap

* [x] Dockerization with Alpine PHP-FPM
* [x] Database Volume Persistence
* [ ] Multi-Tenant Admin Dashboard Integration
* [ ] Novu SDK Implementation for OTPs
* [ ] Two-Way SMS Webhook Controller
* [ ] OpenAI Integration for Sentiment Analysis in SMS Replies
* [ ] Multi-Theme Switching Logic

---

## 👨‍💻 Contribution & Development

If you want to add new modules:

1. **Commands:** Use `docker-compose exec app php artisan make:command`.
2. **Frontend:** Use `npm run dev` (inside a Node container) for Vite HMR.
3. **Testing:** All emails can be viewed at `http://localhost:8025`.

