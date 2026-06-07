# SkyCast Frontend (Vue SPA)

The standalone single-page app for SkyCast Pro. It talks to the Laravel API
(`../`, default `http://localhost:8000/api`) over `axios` using a Sanctum bearer
token stored in `localStorage`.

## Stack

- Vue 3 (`<script setup>` + TypeScript)
- Vue Router (route guards for auth / admin / verified)
- Pinia (`stores/auth.ts` — token + user)
- axios (`lib/axios.ts` — base URL, Bearer interceptor, 401 handling)
- Tailwind CSS + Vite

## Setup

```bash
pnpm install
cp .env.example .env     # VITE_API_URL=http://localhost:8000
pnpm dev                 # http://localhost:5173
```

Make sure the Laravel API is running on `http://localhost:8000` and that its
`FRONTEND_URL` / CORS allow `http://localhost:5173`.

## Scripts

```bash
pnpm dev       # start the dev server
pnpm build     # type-check (vue-tsc) + production build to dist/
pnpm preview   # preview the production build
```

## Structure

```
src/
├── main.ts            # app bootstrap (Pinia, router, axios 401 handler)
├── App.vue            # <RouterView> + global toast
├── lib/
│   ├── axios.ts       # axios instance + token storage + interceptors
│   ├── toast.ts       # lightweight toast notifications
│   └── errors.ts      # Laravel 422 -> { field: message } helpers
├── stores/auth.ts     # login / register / logout / fetchUser
├── router/index.ts    # routes + navigation guards
├── components/        # buttons, inputs, modal, navbar, toast
├── layouts/           # GuestLayout, AuthenticatedLayout
└── pages/             # Welcome, auth/*, Dashboard, cities/*, admin/*, profile/*
```
