# CRM Port

A portfolio CRM project built with Laravel and Filament.

## Overview

CRM Port is an admin-focused web application for managing business data through a modern Laravel stack.
It includes role-based access control, activity tracking, and a clean Filament admin interface.

## Main Features

- Filament admin panel
- Role and permission management (Spatie Permission)
- Activity logging (Spatie Activitylog)
- Livewire-powered admin interface
- Pest test suite
- Code formatting with Laravel Pint

## Tech Stack

- PHP 8.2+
- Laravel 12
- Filament 4
- Livewire + Alpine.js
- Tailwind CSS + Vite
- SQLite
- Pest 4

---

## Run With Docker

This project is intended to be run with Docker.

### Requirements

- Docker Desktop (or Docker Engine + Docker Compose)

### 1) Clone repository

```bash
git clone <repo-url>
cd my-port-app
```

### 2) Configure environment

Create `.env` if it does not exist:

```bash
cp .env.example .env
```

Set (or verify) these database values in `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/var/lib/sqlite/database.sqlite
```

### 3) Build and start containers

```bash
docker compose up --build -d
```

### 3.1) Optional: auto-reload frontend changes (recommended for development)

Start Vite watcher container:

```bash
docker compose --profile dev up -d vite
```

When `vite` is running, changes in `resources/css`, `resources/js`, and Blade templates reload automatically.
Open app at [http://localhost:8000](http://localhost:8000).

### 4) Open the app

- App: [http://localhost:8000](http://localhost:8000)
- Admin panel: [http://localhost:8000/admin](http://localhost:8000/admin)

### Daily Use (Copy/Paste)

```bash
# Start project (normal day)
docker compose up -d

# Open logs when needed
docker compose logs -f app
docker compose logs -f web

# Backend changed (PHP/Laravel/routes/config/composer)
docker compose up -d --build app web

# Frontend live mode ON (auto-reload)
docker compose --profile dev up -d vite
docker compose logs -f vite

# Frontend live mode OFF (save CPU)
docker compose stop vite

# Artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan optimize:clear

# Full reset (only if really needed)
docker compose down
docker compose up -d --build
```

---

## Test Accounts

After running `migrate --seed`:

| Role    | Email               | Password |
|---------|---------------------|----------|
| Admin   | admin@example.com   | password |
| Manager | manager@example.com | password |

---

## Useful Docker Commands

```bash
# Stop containers
docker compose down

# Stop and remove volumes (full DB reset)
docker compose down -v

# View logs
docker compose logs -f app
docker compose logs -f web
docker compose logs -f vite

# Run Artisan commands inside app container
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
docker compose exec app php artisan about

# Rebuild backend services after PHP/backend changes
docker compose up -d --build app web

# Start/stop Vite live reload when needed
docker compose --profile dev up -d vite
docker compose stop vite
```

---

## Testing

Run test suite inside Docker:

```bash
docker compose exec app php artisan test
```

---

## Code Style

Run Pint inside Docker:

```bash
docker compose exec app ./vendor/bin/pint
```

Check-only mode (no changes):

```bash
docker compose exec app ./vendor/bin/pint --test
```

---

## Common Troubleshooting

### App does not open on `localhost:8000`

- Check container status:
  ```bash
  docker compose ps
  ```
- Check app logs:
  ```bash
  docker compose logs -f app
  ```

### Database connection error

- Verify `.env` uses SQLite:
  ```env
  DB_CONNECTION=sqlite
  DB_DATABASE=/var/lib/sqlite/database.sqlite
  ```

### Missing app key error

Generate key inside container:

```bash
docker compose exec app php artisan key:generate
```

---

## Project Structure (Short)

```text
my-port-app/
├── app/
├── database/
├── resources/
├── routes/
├── tests/
├── Dockerfile
├── docker-compose.yml
├── composer.json
└── README.md
```
