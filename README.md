# Enterprise CMS

A production-ready, modular Content Management System built with **Laravel 12** and **React 19**.

## Architecture

**Modular Monolith** — clean bounded contexts, ready for extraction to microservices.

```
Client (React SPA) → Cloudflare → Nginx → Laravel (Modular Monolith)
                                             ├── Auth Module
                                             ├── Content Module
                                             ├── Media Module
                                             ├── Analytics Module
                                             ├── Notification Module
                                             └── Core Module (RBAC, Settings, Audit)
```

## Tech Stack

| Layer          | Technology                                          |
|----------------|-----------------------------------------------------|
| **Backend**    | Laravel 12, PHP 8.4, PostgreSQL 16, Redis 7         |
| **Frontend**   | React 19, TypeScript, Vite 6, Tailwind CSS v4       |
| **Auth**       | JWT + Sanctum, TOTP 2FA, RBAC (spatie/permission)   |
| **UI**         | shadcn/ui, Lucide Icons, Tiptap Editor              |
| **Queue**      | Laravel Horizon + Redis                              |
| **Real-time**  | Laravel Reverb (WebSocket)                           |
| **Storage**    | MinIO (dev) / AWS S3 (prod)                          |
| **Monitoring** | Sentry, Prometheus + Grafana                         |
| **CI/CD**      | GitHub Actions, Docker                               |

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Node.js 20+
- PHP 8.3+ & Composer (for local development)

### Development Setup

```bash
# 1. Clone the repository
git clone https://github.com/RusselF/CMS-Enterprise.git
cd CMS-Enterprise

# 2. Copy environment files
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# 3. Start Docker services
docker compose up -d

# 4. Install backend dependencies & setup
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan jwt:secret
docker compose exec php php artisan migrate --seed

# 5. Install frontend dependencies & start dev server
cd frontend
npm install
npm run dev
```

### Access Points

| Service        | URL                          |
|----------------|------------------------------|
| Frontend       | http://localhost:5173         |
| Backend API    | http://localhost/api/v1/      |
| MinIO Console  | http://localhost:9001         |
| Mailpit        | http://localhost:8025         |
| Horizon        | http://localhost/horizon      |

### Default Admin Account

```
Email:    admin@cms-enterprise.test
Password: password
```

## Project Structure

```
cms-enterprise/
├── backend/          # Laravel 12 API
├── frontend/         # React 19 SPA
├── docker/           # Docker configs
├── docker-compose.yml
└── .github/workflows # CI/CD
```

## License

Proprietary — All rights reserved.
