# SkyCast Pro

🌤️ Professional Weather Monitoring System — a **decoupled** application with a **Laravel 13 JSON API** backend and a standalone **Vue 3 SPA** frontend.

This repository hosts two independently-running apps:

| App         | Stack                                              | Dev URL                 |
| ----------- | -------------------------------------------------- | ----------------------- |
| **Backend** | Laravel 13 API · Sanctum tokens · Spatie Permission | `http://localhost:8000` |
| **Frontend** (`frontend/`) | Vue 3 · Vue Router · Pinia · axios · Tailwind · Vite | `http://localhost:5173` |

The browser loads the SPA from **:5173**, and the SPA calls the API at **:8000/api** over `axios`, authenticating with a **Sanctum bearer token** (stored in `localStorage`).

```
Browser ──> Vue SPA (:5173) ──axios + Bearer token──> Laravel API (:8000/api)
```

## ✨ Features

- 🧩 **Decoupled architecture** — Vue SPA and Laravel API are fully separate apps
- 🔐 **Token auth** — Laravel Sanctum personal access tokens (`Authorization: Bearer`)
- 🌍 **Weather integration** — real-time data from the Open-Meteo API
- 👥 **Roles & permissions** — fine-grained access control with Spatie Laravel-Permission
- 📧 **Full auth flows** — register, email verification, password reset, password confirmation
- 📱 **Modern UI** — Tailwind CSS, dark mode, responsive
- 🚀 **TypeScript** — type-safe frontend with Pinia state management

## 📋 Requirements

- **PHP** 8.3+ and **Composer**
- **MySQL** (or adjust `.env` for another driver)
- **Node.js** 20.19+ and **pnpm** 10+ (`corepack enable pnpm`)

## 🚀 Quick Start

You need **two terminals**: one for the API, one for the SPA.

### 1. Backend (Laravel API)

```bash
# from the repository root
composer install
cp .env.example .env
php artisan key:generate

# configure your DB in .env, then:
php artisan migrate --seed
php artisan serve            # serves the API on http://localhost:8000
```

Relevant `.env` values:

```dotenv
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173   # used for CORS + reset/verification links

DB_CONNECTION=mysql
DB_DATABASE=weather_app
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log   # verification/reset emails are written to storage/logs
```

The seeder creates the `admin`/`user` roles and two demo accounts:

| Role  | Email               | Password   |
| ----- | ------------------- | ---------- |
| Admin | `admin@weather.com` | `password` |
| User  | `user@weather.com`  | `password` |

> The seeded accounts are created already-verified, so you can log in immediately.

### 2. Frontend (Vue SPA)

```bash
cd frontend
pnpm install
cp .env.example .env         # VITE_API_URL=http://localhost:8000
pnpm dev                     # serves the SPA on http://localhost:5173
```

Open **http://localhost:5173** in your browser.

## 🔐 Auth flow

1. The SPA `POST`s credentials to `/api/login` (or `/api/register`).
2. The API returns `{ token, user }`. The SPA stores the token in `localStorage`.
3. Every subsequent request sends `Authorization: Bearer <token>` (axios interceptor).
4. `POST /api/logout` revokes the current token.

Email verification & password reset links are generated pointing at the **SPA**
(`FRONTEND_URL`), which then forwards the signed parameters back to the API — see
`app/Providers/AppServiceProvider.php`. With `MAIL_MAILER=log`, the links appear in
`storage/logs/laravel.log` for local testing.

## 📡 API Endpoints

All routes are prefixed with `/api`.

| Method | Path | Auth | Description |
| ------ | ---- | ---- | ----------- |
| POST | `/register` | — | Create account, returns token + user |
| POST | `/login` | — | Returns token + user |
| POST | `/forgot-password` | — | Email a reset link |
| POST | `/reset-password` | — | Reset password with token |
| GET | `/email/verify/{id}/{hash}` | signed | Verify email (from the email link) |
| GET | `/user` | token | Current user (roles, permissions, flags) |
| POST | `/logout` | token | Revoke current token |
| POST | `/email/verification-notification` | token | Resend verification email |
| PUT | `/password` | token | Update password |
| POST | `/confirm-password` | token | Confirm current password |
| PATCH | `/profile` | token | Update name/email |
| DELETE | `/profile` | token | Delete account |
| GET | `/dashboard` | token | Dashboard weather payload |
| GET | `/cities` | token | Paginated/searchable cities |
| GET | `/cities/{city}/weather` | token | Weather for a city |
| POST | `/cities` | token + admin | Create city |
| PUT/PATCH | `/cities/{city}` | token + admin | Update city |
| DELETE | `/cities/{city}` | token + admin | Delete city |
| GET | `/admin/users` | token + admin | Paginated users + roles |
| PATCH | `/admin/users/{user}/role` | token + admin | Change a user's role |

## 🧪 API Tests

Ready-to-run **Bruno** and **Postman** collections live in [`api-tests/`](api-tests).
Both use bearer-token auth: run the *Login* request first and the token is captured
automatically for the rest. See [`api-tests/README.md`](api-tests/README.md).

## 🗄️ Project Structure

```
├── app/Http/Controllers/Api/   # JSON API controllers (Auth, City, Dashboard, …)
├── app/Http/Resources/         # UserResource
├── app/Models/                 # User (HasApiTokens), City
├── app/Services/               # WeatherService, BmkgService
├── routes/api.php              # All API routes
├── config/cors.php             # Allows the SPA origin
├── frontend/                   # Standalone Vue 3 SPA
│   └── src/
│       ├── lib/                # axios instance, toast, error helpers
│       ├── stores/auth.ts      # Pinia auth store (token + user)
│       ├── router/             # Vue Router + guards
│       ├── components/ layouts/ pages/
└── api-tests/                  # Bruno + Postman collections
```

## 🛠️ Common Commands

```bash
# Backend
php artisan serve              # API on :8000
php artisan migrate:fresh --seed
php artisan route:list --path=api
php artisan pint               # code style

# Frontend (run inside frontend/)
pnpm dev                       # SPA on :5173
pnpm build                     # type-check + production build
```

## 📄 License

MIT
