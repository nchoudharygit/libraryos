# LibraryOS — Commands Cheat Sheet

Quick reference for daily dev commands across all 4 services (Catalog, Consumer, Transaction, Gateway).

---

## 1. Starting everything (daily routine)

**One-shot script** (from `libraryos/` root):
```bash
./start-all.sh
```
This starts Postgres, then all 4 services in background, with logs going to `logs/*.log`.

**Stop everything:**
```bash
pkill -f "artisan serve"
```

**Manual Postgres check/start** (if script's Postgres line fails):
```bash
docker ps -a | grep libraryos
docker start libraryos-postgres
```

**Manual per-service start** (if you need just one, or to see live output instead of a log file):
```bash
cd services/catalog-service     && php artisan serve --port=8001
cd services/consumer-service    && php artisan serve --port=8002
cd services/transaction-service && php artisan serve --port=8003
cd services/gateway-service     && php artisan serve --port=8000
```

**Check what's running:**
```bash
ps aux | grep "artisan serve"
```

**Find which folder a running process is using** (useful for "why is this serving the wrong app" bugs):
```bash
ls -la /proc/<PID>/cwd
```

---

## 2. Composer (per service — run from inside that service's folder)

```bash
composer require <package-name>              # install a package
composer show <package-name>                 # check if a package is installed + version
composer require doctrine/dbal                # needed before using ->change() in migrations
```

---

## 3. Artisan — routes & migrations

```bash
php artisan route:list                        # see all registered routes
php artisan make:migration <name> --table=<table>   # new migration for existing table
php artisan migrate                            # run pending migrations
php artisan migrate:status                     # see which migrations have run
```

---

## 4. Artisan — cache clearing (run these if code changes don't seem to take effect)

```bash
php artisan config:clear                       # clear cached config (.env / config/*.php changes)
php artisan route:clear                        # clear cached routes
```

---

## 5. Artisan — controllers & tinker

```bash
php artisan make:controller <Name>Controller   # scaffold a controller
php artisan tinker                             # interactive PHP shell for the app
```
Inside tinker, useful checks:
```php
config('services.auth0.domain')                // verify a config value loaded correctly
User::all()                                     // check DB records directly
exit                                            // leave tinker
```

---

## 6. Testing endpoints with curl

```bash
curl http://localhost:8001/api/v1/health
curl -v http://localhost:8001/api/v1/health     # -v shows headers — useful for CORS debugging
```

---

## 7. Autoload (after moving/renaming a class or namespace)

```bash
composer dump-autoload
```

---

## 8. Git basics (for when you're ready to commit)

```bash
git status
git add .
git commit -m "message here"
git push
```

---

## 9. Common gotchas quick-reference

| Symptom | Likely cause | Fix |
|---|---|---|
| Migration `Connection refused` | Postgres container stopped | `docker start libraryos-postgres` |
| 404 on a route you just added | Route cached, or wrong app is serving | `php artisan route:clear` + check with `route:list` |
| Config value comes back `null` in tinker | `.env` key typo, or config cached | `php artisan config:clear`, recheck `.env` key name |
| `->change()` migration error | Missing Doctrine DBAL | `composer require doctrine/dbal` |
| Browser blocks API request but curl works fine | CORS — `allowed_origins` too strict in `config/cors.php` | Set `'allowed_origins' => ['*']` for local dev, `config:clear` |
| `make:controller` syntax error | Typed `make::controller` (double colon) | Use single colon: `make:controller` |
| Laravel 13: `routes/api.php` missing | Not scaffolded by default | `php artisan install:api` |

---

*Keep this updated as new commands come up — it's meant to save you from re-explaining the same fix twice.*
