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
- MySQL 8
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

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=crm_port
DB_USERNAME=crm_user
DB_PASSWORD=secret
```

### 3) Build and start containers

```bash
docker compose up --build -d
```

### 4) Open the app

- App: [http://localhost:8000](http://localhost:8000)
- Admin panel: [http://localhost:8000/admin](http://localhost:8000/admin)

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
docker compose logs -f db

# Run Artisan commands inside app container
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
docker compose exec app php artisan about
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

- Verify `.env` uses `DB_HOST=db` in Docker mode.
- Ensure DB container is running:
  ```bash
  docker compose logs -f db
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
