Aap ka setup bilkul theek hai 👍 (Laravel + PHP-FPM + Nginx + MySQL + Redis + Mailpit).

Ab main seedha practical tareeqa batata hoon ke **`php artisan`** aur **`composer`** commands kaise chalani hain.

---

# ✅ Step 1: Containers run karein

Project folder me jaa kar:

```bash
docker compose up -d --build
```

Agar pehli dafa build kar rahe hain to `--build` zaroor lagayen.

Check karne ke liye:

```bash
docker ps
```

---

# ✅ Step 2: PHP Artisan Commands kaise chalani hain

Aap ka Laravel PHP container ka naam hai:

```
laravel_app
```

To artisan command chalane ka tareeqa:

```bash
docker exec -it laravel_app php artisan migrate
```

Example commands:

```bash
docker exec -it laravel_app php artisan key:generate
docker exec -it laravel_app php artisan migrate
docker exec -it laravel_app php artisan cache:clear
docker exec -it laravel_app php artisan config:clear
```

---

# ✅ Step 3: Composer Commands kaise chalani hain

Kyuki aap ne Dockerfile me ye line add ki hai:

```dockerfile
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
```

Iska matlab composer already container ke andar install hai ✅

Composer command chalane ka tareeqa:

```bash
docker exec -it laravel_app composer install
```

Ya:

```bash
docker exec -it laravel_app composer update
```

---

# ✅ Alternative (Better & Clean Way)

Container ke andar enter ho jao:

```bash
docker exec -it laravel_app sh
```

Phir andar:

```bash
php artisan migrate
composer install
```

Exit karne ke liye:

```bash
exit
```

---

# 🔥 Best Practice (Short Command)

Agar aap baar baar command chalate hain to ye use karein:

```bash
docker compose exec app php artisan migrate
```

Aur composer ke liye:

```bash
docker compose exec app composer install
```

Ye `container_name` ke bajaye service name (`app`) use karta hai — jo zyada professional tareeqa hai.

---

# 🌐 Browser Access

Aap ka nginx port mapping hai:

```
8000:80
```

To browser me open karein:

```
http://localhost:8000
```

---

# 🎯 Summary

| Kaam            | Command                                       |
|-----------------|-----------------------------------------------|
| Container start | `docker compose up -d --build`                |
| Artisan run     | `docker compose exec app php artisan migrate` |
| Composer run    | `docker compose exec app composer install`    |
| Container shell | `docker compose exec app sh`                  |

---

Agar aap chahen to main aap ko **complete production-ready Laravel Docker setup** bhi bana kar de sakta hoon (
optimized + faster + smaller image).
