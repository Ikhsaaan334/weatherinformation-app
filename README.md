# SkyCast Pro

Professional Weather Monitoring System built with Laravel, Inertia.js, Vue 3, and Vite.

## Features

- Inertia + Vue 3 single-page experience
- Authentication scaffolding (Breeze)
- City weather pages powered by BMKG data
- Role/permission support (Spatie)

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- npm

## Setup (local)

1. Install PHP dependencies

    composer install

2. Install JS dependencies

    npm install

3. Copy example environment and generate key

    cp .env.example .env
    php artisan key:generate

4. Configure database in `.env` and run migrations & seeders

    php artisan migrate --seed

5. Start dev servers

```bash
# start Laravel server (terminal A)
php artisan serve

# start Vite dev server (terminal B)
npm run dev
```

Open your browser at http://127.0.0.1:8000

## Build for production

```bash
npm run build
php artisan migrate --force
```

## Notes

- The project uses Inertia's script-based initial page payload; ensure `public/hot` points to a reachable Vite dev server when developing.
- If TypeScript reports missing declarations for `@inertiajs/vue3`, ensure `resources/js/types/*.d.ts` are included in `tsconfig.json`.

## Contributing

Feel free to open issues or PRs. Follow PSR-12 and run linters/formatters where applicable.

## License

MIT
