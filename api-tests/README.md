# API tests (Bruno & Postman)

Manual/automated request collections for the SkyCast Pro endpoints.

> **Why this is special:** this app uses **Inertia.js**, not a token REST API.
> Auth is **session + CSRF based**, and responses depend on the `X-Inertia` header.
> The collections handle all of that for you via scripts — you just run the requests in order.

## How Inertia responds

| Request | Behaviour |
| ------- | --------- |
| No `X-Inertia` header | Server returns the **full HTML page** (status 200) |
| `X-Inertia: true` | Server returns **JSON**: `{ component, props, url, version }` |
| Wrong/missing `X-Inertia-Version` on a GET | Server returns **409** (asset version changed) |
| Validation fails (with `X-Inertia`) | **422** with JSON errors |
| Blocked by `role:admin` | **403** |
| Success on POST/PUT/PATCH/DELETE | A **redirect** (302/303) which the client follows |

## What the scripts do automatically

1. **CSRF** — `00 Bootstrap` loads `/login` as HTML; the script reads the `XSRF-TOKEN`
   cookie and re-reads it after every response (login/logout rotate the session). It is
   sent back as the `X-XSRF-TOKEN` header on every request.
2. **Asset version** — the script captures Inertia's `version` (from the HTML `data-page`
   or the JSON body) and sends it as `X-Inertia-Version`, so GET pages don't 409.
3. **Session cookie** — handled by the tool's cookie jar (keep it enabled).

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

1. **00 Bootstrap** — gets the CSRF token + asset version (run once per session).
2. **01 Login (admin)** — authenticate as admin.
3. **02–09** — dashboard, cities CRUD, city weather, admin user management.
4. **10 Logout** when done.
5. Role-enforcement check: **12 Login (user)** → **13** and **14** should both return **403**.

> `11 Register` creates a new account; change the email between runs (emails are unique).

---

### Bruno

1. Install [Bruno](https://www.usebruno.com/).
2. **Open Collection** → select `api-tests/bruno`.
3. Pick the **Local** environment (top-right).
4. Settings → make sure **Cookies** are enabled.
5. Run requests top-to-bottom. Each request has assertions under the **Assert/Tests** tab.

### Postman

1. **Import** `api-tests/postman/SkyCast.postman_collection.json`.
2. **Import** `api-tests/postman/SkyCast.postman_environment.json` and select it.
3. Settings → **Automatically follow redirects: ON**, and cookie jar enabled (default).
4. Run requests in order, or use the **Collection Runner**.

## Adjusting data

Edit env vars (`cityId`, `targetUserId`) to point at rows that exist in your DB.
After `migrate:fresh --seed`, city ids start at 1 and the admin/user are ids 1 and 2.
