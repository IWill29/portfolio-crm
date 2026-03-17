# CRM Port (Filament + Laravel)

This repository is a Laravel + Filament CRM solution featuring:
- Spatie Permissions (roles & permissions)
- Spatie Activity Log (recent activity widget)
- Filament UI + dashboards + widgets
- Livewire reactive admin interface

---

## Technology Stack

- **Backend:** Laravel 12.0, Filament 4.0, PHP 8.2+
- **Frontend:** Livewire, Alpine.js, Tailwind CSS 4.0, Vite 7.0
- **Database:** MySQL/PostgreSQL with migrations
- **Testing:** Pest 4.4
- **Package Management:** Composer
- **Packages:** Spatie Permission, Activity Log, Media Library

## Project Structure

```
my-port-app/
├── app/
│   ├── Enums/                 # Type-safe enums (Project statuses, priorities)
│   ├── Filament/              # Admin panel resources & widgets
│   ├── Models/                # Eloquent ORM models (User, Client, Project)
│   ├── Policies/              # Authorization rules (RBAC)
│   └── Traits/                # LogsActivity trait (activity tracking)
├── database/
│   ├── migrations/            # Database schema definitions
│   └── seeders/               # Initial data & test users
├── resources/views/           # Public website pages
├── tests/                      # Pest test suite
├── .env.example               # Environment variables template
└── composer.json              # PHP dependencies
```

### Key Directories

| Directory | Purpose |
|-----------|---------|
| **`app/Enums/`** | Type-safe enums (project statuses, priorities) |
| **`app/Filament/`** | Admin panel resources and widgets |
| **`app/Models/`** | Eloquent models with relationships |
| **`app/Policies/`** | Authorization rules (admin/manager/user) |
| **`app/Traits/`** | LogsActivity - automatic activity tracking |
| **`database/migrations/`** | Database schema |
| **`database/seeders/`** | Initial data and test accounts |
| **`resources/views/`** | Public pages (homepage) |
| **`tests/`** | Pest test suite |

## Getting Started (Local)

### 1) Clone
```bash
git clone <repo-url>
cd my-port-app
```

### 2) Install dependencies
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3) Database setup
```bash
php artisan migrate --seed
```

### 4) Run server
```bash
php artisan serve
```

---

## Test Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Manager | manager@example.com | password |

Login at: `http://localhost:8000/admin`
