# Laravel Docker Setup Guide

This project runs **Laravel with Docker** using:

* PHP-FPM (Alpine)
* Nginx
* MySQL
* Redis (Redis Stack)
* Mailpit (Email testing)

---

# 🐳 1. Starting the Containers

Navigate to the project root directory and run:

```bash
docker compose up -d --build
```

> Use `--build` when running for the first time or after modifying the Dockerfile.

To verify running containers:

```bash
docker compose ps
```

---

# 🌐 2. Accessing the Application

The application is exposed via Nginx on:

```
http://localhost:8000
```

---

# 🛠 3. Running Artisan Commands

The recommended way (best practice):

```bash
docker compose exec app php artisan migrate
```

Common examples:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

---

# 📦 4. Running Composer Commands

Since Composer is installed inside the container, run:

```bash
docker compose exec app composer install
```

or

```bash
docker compose exec app composer update
```

---

# 🖥 5. Entering the Container Shell

If you prefer interactive mode:

```bash
docker compose exec app sh
```

Then inside the container:

```bash
php artisan migrate
composer install
```

Exit with:

```bash
exit
```

---

# 🗄 6. Database Configuration (.env)

Update your `.env` file as follows:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=root
```

⚠ Important:
`DB_HOST` must be `db` (the service name), not `localhost`.

---

# 🔴 7. Redis Configuration (.env)

```env
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null
```

Redis Insight Dashboard:

```
http://localhost:8001
```

---

# ✉ 8. Mailpit Configuration (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Mailpit UI:

```
http://localhost:8025
```

---

# 🔐 9. Fixing Permissions (Very Important)

If you encounter storage or cache write errors, run:

### Change Owner:

```bash
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
```

### Set Write Permissions:

```bash
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

---

# 🧪 10. Debugging & Troubleshooting

---

## ❌ 502 Bad Gateway

Check logs:

```bash
docker compose logs nginx
docker compose logs app
```

Possible causes:

* PHP-FPM not running
* Wrong `fastcgi_pass` value
* Container crash

---

## ❌ Database Connection Error

Make sure:

* `.env` uses `DB_HOST=db`
* MySQL container is running:

```bash
docker compose ps
```

Restart containers if needed:

```bash
docker compose down
docker compose up -d
```

---

## ❌ Redis Error: `Class "Redis" not found`

This means the Redis PHP extension is missing.

Rebuild containers after updating Dockerfile:

```bash
docker compose down
docker compose up -d --build
```

Verify Redis extension:

```bash
docker compose exec app php -m | grep redis
```

---

## ❌ Changes Not Reflecting?

Clear Laravel caches:

```bash
docker compose exec app php artisan optimize:clear
```

---

# 🔄 11. Restarting Everything

```bash
docker compose down
docker compose up -d --build
```

---

# 📌 Quick Command Summary

| Task             | Command                                       |
|------------------|-----------------------------------------------|
| Start containers | `docker compose up -d --build`                |
| Stop containers  | `docker compose down`                         |
| Run Artisan      | `docker compose exec app php artisan migrate` |
| Run Composer     | `docker compose exec app composer install`    |
| Enter container  | `docker compose exec app sh`                  |
| View logs        | `docker compose logs app`                     |

---

# 🚀 Production Notes

For production environments, consider:

* Using a multi-stage Docker build
* Disabling debug mode (`APP_DEBUG=false`)
* Running queue workers in a separate container
* Setting up a scheduler container
* Using environment-specific `.env` files
* Using non-root database credentials

---

# ✅ System Architecture Overview

| Service           | Container | Port        |
|-------------------|-----------|-------------|
| Laravel (PHP-FPM) | app       | 9000        |
| Nginx             | nginx     | 8000        |
| MySQL             | db        | 3306        |
| Redis Stack       | redis     | 6379 / 8001 |
| Mailpit           | mailpit   | 8025        |

---

Your Laravel Docker environment is now fully documented and ready for development 🚀
