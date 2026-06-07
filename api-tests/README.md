# API tests (Bruno & Postman)

Request collections for the SkyCast **JSON API** (`/api/*`).

> Auth is **Sanctum bearer tokens**. You log in once, the collection captures the
> returned token, and every later request is sent with `Authorization: Bearer <token>`.
> No CSRF, cookies, or Inertia headers involved.

## How auth works in these collections

1. **01 Login (admin)** `POST /api/login` → the response `{ token, user }` is captured
   into the `token` variable.
2. A collection-level **pre-request script** adds `Authorization: Bearer {{token}}` to
   every request automatically.
3. **12 Login (user)** overwrites `token` with the regular user's token so the negative
   tests (13, 14) run as a non-admin.
4. **10 Logout** revokes the token server-side.

`baseUrl` already includes the `/api` prefix (e.g. `http://127.0.0.1:8000/api`).

## Prerequisites

```bash
# from the project root
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed   # creates admin@weather.com / user@weather.com (pw: password)
php artisan serve                  # http://127.0.0.1:8000
```

Seeded accounts:

| Role  | Email               | Password   |
| ----- | ------------------- | ---------- |
| Admin | `admin@weather.com` | `password` |
| User  | `user@weather.com`  | `password` |

## Run order

1. **01 Login (admin)** — authenticate; token is stored automatically.
2. **02–09** — dashboard, cities CRUD (create→update→delete a throwaway city), city
   weather, admin user management.
3. **10 Logout**.
4. **11 Register** — creates a new account (change the email between runs).
5. **12 Login (user)** → **13** and **14** must both return **403** (role enforcement).

---

### Bruno

1. Install [Bruno](https://www.usebruno.com/).
2. **Open Collection** → select `api-tests/bruno`.
3. Pick the **Local** environment (top-right).
4. Run requests top-to-bottom (or `bru run --env Local`). Assertions live under each
   request's **Assert/Tests** tab.

### Postman

1. **Import** `api-tests/postman/SkyCast.postman_collection.json`.
2. **Import** `api-tests/postman/SkyCast.postman_environment.json` and select it.
3. Run requests in order, or use the **Collection Runner** (the token carries over
   between requests via the environment variable).

## Adjusting data

Edit env vars (`cityId`, `targetUserId`) to point at rows that exist in your DB.
After `migrate:fresh --seed`, city ids start at 1 and the admin/user are ids 1 and 2.
The create/update/delete steps operate on a `newCityId` captured at runtime, so they
never touch the seeded cities.
