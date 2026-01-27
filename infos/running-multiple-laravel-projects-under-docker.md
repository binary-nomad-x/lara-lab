# 🌐 Comprehensive Guide: Running Multiple Laravel Projects with Docker

This guide provides a deep dive into isolating multiple Laravel environments on a single host machine without conflicts.

---

## 🏗️ 1. Architecture Overview

Each Laravel project runs in its own isolated network. To avoid "Address already in use" errors, we must ensure that no
two containers attempt to bind to the same **Host Port** (the port on your Windows machine).

---

## 🔑 2. The "Rule of Three" for New Projects

When cloning or starting a new project (e.g., `project-two`), you **must** unique-ify three things in your
`docker-compose.yml`:

### A. Service Container Names

Docker requires unique container names across the entire engine.

* **Bad:** `container_name: laravel_app` (If Project 1 is already using it).
* **Good:** `container_name: p2_app`, `container_name: p2_db`, `container_name: p2_nginx`.

### B. Host Port Mapping

Only one service can listen on a Windows port at a time.

* **Project 1:** `8000:80` (Web) | `3306:3306` (DB)
* **Project 2:** `8001:80` (Web) | `3307:3306` (DB)
* **Project 3:** `8002:80` (Web) | `3308:3306` (DB)

### C. Internal Network Names

To prevent cross-talk between projects, use unique network names.

```yaml
networks:
  p2_network:      # Instead of 'laravel_net'
    driver: bridge

```

---

## 📝 3. Optimized `docker-compose.yml` Template

Copy this for your secondary projects and just update the prefix (e.g., change `p2` to your project name).

```yaml
services:
  # PHP-FPM Service
  app:
    build: .
    container_name: p2_php_app
    volumes:
      - .:/var/www/html
    networks:
      - p2_network

  # Nginx Web Server
  nginx:
    image: nginx:trixie-perl
    container_name: p2_nginx_server
    ports:
      - "8001:80"             # <--- Unique Web Port
    volumes:
      - .:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - p2_network

  # MySQL Database
  db:
    image: mysql:latest
    container_name: p2_mysql_db
    ports:
      - "3307:3306"           # <--- Unique DB Port for DataGrip
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: p2_database
    networks:
      - p2_network

networks:
  p2_network:
    driver: bridge

```

---

## ⚙️ 4. Workflow Commands (No Local PHP/Composer)

Since your host machine is clean, use the container aliases:

### 📥 Dependency Management

Run this from your project root to install `vendor` folder:

```powershell
docker run --rm -v ${PWD}:/app composer install

```

### 🚀 Bootstrapping the App

```powershell
# Start containers
docker-compose up -d

# Generate key (inside the specific project container)
docker exec p2_php_app php artisan key:generate

# Run Migrations
docker exec p2_php_app php artisan migrate

```

---

## 🗄️ 5. Database Connection Matrix (DataGrip/HeidiSQL)

To connect your Windows GUI tools to the Docker Database:

| Setting             | Value                          | Note                                      |
|---------------------|--------------------------------|-------------------------------------------|
| **Connection Type** | MySQL                          |                                           |
| **Host**            | `127.0.0.1`                    | Always use IP to bypass DNS issues        |
| **Port**            | `3307`                         | Use the **Left-side** port from your YAML |
| **User/Pass**       | `root` / `root`                | As defined in `environment` section       |
| **Driver Property** | `allowPublicKeyRetrieval=true` | Required for MySQL 8+                     |

---

## 🛠️ 6. Common Troubleshooting

* **Zombie Containers:** If you see `Bind for 0.0.0.0:3306 failed`, another project or a local MySQL is running. Run
  `docker ps` to find it and `docker stop <id>` to kill it.
* **Permissions:** If Laravel logs show "Permission Denied", run:
  `docker exec p2_php_app chmod -R 777 storage bootstrap/cache`
* **Network Cleanup:** If you have too many unused networks:
  `docker network prune`

