# 🚀 Laravel Docker Environment: Daily Operations Guide

Since PHP and Composer are not installed on your host machine, all operations must be routed through Docker containers. This guide outlines the essential commands and integration steps for your development tools.

---

## 🛠 1. Essential Daily Commands

### Container Management

Use these commands to manage the lifecycle of your development environment:

* **`docker-compose up -d`** Starts all services (PHP, MySQL, Nginx, etc.) in the background. Use this to begin your workday.
* **`docker-compose stop`** Gracefully pauses running containers. Use this if you want to save system resources but keep the current state.
* **`docker-compose down`** Stops and **removes** all containers and the internal network. Use this when you are finished with the project or need to reset the environment.

### Laravel Artisan Commands

To run Artisan commands, you must execute them inside the `app` container:

* **Run Migrations:** `docker-compose exec app php artisan migrate`
* **Create Components:** `docker-compose exec app php artisan make:controller OrderController`
* **Clear Cache:** `docker-compose exec app php artisan config:clear`

### Composer Dependency Management

Since Composer is not on your PC, we use a temporary "one-off" container to handle packages:

* **Install Package:** `docker run --rm -v ${PWD}:/app composer require laravel/breeze`
*(The `--rm` flag ensures the container is deleted immediately after the task is finished).*

---

## 🗄 2. Database Integration (DataGrip / GUI Tools)

Docker maps the container's internal MySQL port to your computer. DataGrip treats the database as if it were running locally.

**Connection Settings:**

1. **Data Source:** Select **MySQL**.
2. **Host:** `127.0.0.1` (Always preferred over `localhost` to avoid DNS lag).
3. **Port:** `3306` (The host port defined in your `docker-compose.yml`).
4. **User:** `root`
5. **Password:** `root`
6. **Database:** `laravel_db`

> **Pro Tip:** If the connection is refused, ensure no local MySQL service (like XAMPP) is already occupying port `3306`. If it is, you must stop the local service or change the port mapping in `docker-compose.yml` to `3307:3306`.

---

## 💻 3. PHPStorm Integration & Tinker Setup

To get full IDE support (code completion) and use the **Tinker Console** plugin, you must link PHPStorm to the Dockerized PHP interpreter.

### Step 1: Configure Remote Interpreter

1. Navigate to **File > Settings > Languages & Frameworks > PHP**.
2. Click the **`...`** button next to **CLI Interpreter**.
3. Click the **`+`** icon and select **"From Docker, Vagrant, VM..."**.
4. Choose **Docker Compose**.
5. Set **Service** to `app` (this targets your Laravel container).
6. Apply and Save. PHPStorm will now index the PHP version inside the container.

### Step 2: Running Tinker

1. Open the **Tinker Console** plugin.
2. The plugin will prompt you for an interpreter; select the **Docker Compose Interpreter** you just created.
3. You can now run interactive PHP code directly against your live database and application logic.

---

## 💡 Summary Table for Quick Reference

| Action | Command / Logic |
| --- | --- |
| **Start Project** | `docker-compose up -d` |
| **Install Vendor** | `docker run --rm -v ${PWD}:/app composer install` |
| **Artisan** | `docker-compose exec app php artisan [command]` |
| **DB Host** | `127.0.0.1` |
| **DB Port** | `3306` |

