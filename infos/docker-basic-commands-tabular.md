### 🐳 Docker Command Reference Table

| Category | Command | Function / Description |
| --- | --- | --- |
| **Status** | `docker ps` | Lists all **currently running** containers. |
| **Status** | `docker ps -a` | Lists **all** containers (including stopped ones). |
| **Images** | `docker images -a` | Shows all downloaded/built images on your system. |
| **Startup** | `docker-compose up -d` | Starts containers in **Detached mode** (runs in background). |
| **Startup** | `docker-compose up -d --build` | Re-builds the `Dockerfile` and then starts containers. |
| **Shutdown** | `docker-compose stop` | Stops the containers but keeps them created. |
| **Shutdown** | `docker-compose down` | Stops and **removes** containers, networks, and images. |
| **Interactive** | `docker exec -it <name> sh` | Opens a **Terminal/Shell** inside a container. |
| **Interactive** | `docker exec -it <name> mysql -u root -p` | Enters the **MySQL Shell** inside the DB container. |
| **Execution** | `docker-compose exec app <cmd>` | Runs a specific command (like `php artisan`) inside the app. |
| **One-off** | `docker run --rm -v ${PWD}:/app composer <cmd>` | Runs a container just for one task (like `composer install`) then deletes it. |
| **Logs** | `docker logs -f <name>` | **Follows** live logs (useful for debugging errors). |
| **Cleanup** | `docker system prune` | Deletes all unused data (stopped containers, dangling images). |

---

### 💡 Specific Examples for your Laravel Setup

| Task | Exact Command to Type |
| --- | --- |
| **See Running Ports** | `docker ps` |
| **Run Migrations** | `docker-compose exec app php artisan migrate` |
| **Enter App Terminal** | `docker exec -it laravel_app sh` |
| **Enter DB Terminal** | `docker exec -it laravel_db mysql -u root -proot` |
| **Check Error Logs** | `docker logs laravel_app` |
| **Install Package** | `docker run --rm -v ${PWD}:/app composer require <package>` |

### 🔑 Quick Definitions:

* **`-d` (Detached):** Frees up your terminal so you can keep typing while the server runs.
* **`-it` (Interactive TTY):** Keeps the connection open so you can "talk" to the container (type commands).
* **`--rm`:** Automatically cleans up the container after the command finishes.
* **`exec` vs `run`:** Use `exec` for containers already running (like your web server). Use `run` for a fresh container to do a quick task (like composer).

