# 🐳 Laravel Docker Integration Manual

This guide provides a quick reference for managing your Laravel development environment using Docker and Docker Compose.

## 🛠 Core Docker Management

| Category | Command | Description |
| --- | --- | --- |
| **Status** | `docker ps` | View currently running containers and their ports. |
| **Status** | `docker ps -a` | View all containers (including exited/stopped ones). |
| **Images** | `docker images -a` | List all local images and their disk usage. |
| **Startup** | `docker-compose up -d` | Start all services in the background (Detached). |
| **Rebuild** | `docker-compose up -d --build` | Re-read `Dockerfile` changes and restart services. |
| **Shutdown** | `docker-compose stop` | Stop services (keeps containers intact). |
| **Purge** | `docker-compose down` | Stop and **remove** containers and networks (clean slate). |
| **Logs** | `docker-compose logs -f app` | Tail live PHP/Laravel logs to debug errors. |
| **Cleanup** | `docker system prune -a` | **Danger:** Deletes all unused images and stopped containers. |

---

## 🚀 Laravel & Composer Commands

*Since Composer is now integrated into the `app` service via the Dockerfile.*

| Task | Command | Note |
| --- | --- | --- |
| **Install Dependencies** | `docker-compose exec app composer install` | Uses the `composer.lock` file. |
| **Update Dependencies** | `docker-compose exec app composer update` | Refreshes `composer.lock` based on `.json`. |
| **Run Migrations** | `docker-compose exec app php artisan migrate` | Syncs database tables. |
| **Fresh Migration** | `docker-compose exec app php artisan migrate:fresh` | **Wipes DB** and re-runs all migrations. |
| **Optimize/Clear Cache** | `docker-compose exec app php artisan optimize:clear` | Clears config, route, and view cache. |
| **Tinker** | `docker-compose exec app php artisan tinker` | Open the interactive PHP shell. |

---

## 💾 Database & Tools

| Task | Command | Description |
| --- | --- | --- |
| **MySQL Shell** | `docker exec -it laravel_db mysql -u root -proot` | Direct access to the MySQL CLI. |
| **App Shell** | `docker exec -it laravel_app sh` | Enter the Alpine Linux shell of your app. |
| **Node/NPM** | `docker run --rm -v ${PWD}:/app -w /app node:latest npm install` | Run NPM without installing Node on Windows. |
| **Create Project** | `docker run --rm -v ${PWD}:/app composer create-project laravel/laravel .` | Start a brand new Laravel project in a blank folder. |

---

## 🔑 Key Concepts for Success

* **`exec` vs `run**`:
* Use **`exec`** when your containers are already running (most common).
* Use **`run --rm`** for "one-off" tasks where you don't have a service running (like a fresh install).


* **Volume Syncing (`-v`)**: The `.:/var/www/html` mapping ensures that any code you save in VS Code on Windows is instantly updated inside the Linux container.
* **The `.env` Factor**: Always ensure your `DB_HOST` in `.env` matches your service name in `docker-compose.yml` (in your case, `DB_HOST=db`).

---

### 💡 Pro-Tip: PowerShell Alias

Tired of typing `docker-compose exec app`? Add this to your PowerShell `$PROFILE`:

```powershell
function pa { docker-compose exec app php artisan $args }
function dcomp { docker-compose exec app composer $args }

```

Now you can just type: **`pa migrate`** or **`dcomp install`**.
