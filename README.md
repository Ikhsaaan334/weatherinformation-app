# SkyCast Pro

🌤️ Professional Weather Monitoring System built with **Laravel 13**, **Inertia.js**, **Vue 3**, and **Vite**.

A modern, reactive weather information application that provides real-time weather data with role-based access control and a seamless user experience.

## ✨ Features

- ⚡ **Inertia + Vue 3** - Full-stack reactivity with server-driven UI
- 🔐 **Authentication** - Built-in Laravel Breeze authentication scaffolding
- 🌍 **Weather Integration** - Real-time weather data powered by BMKG API
- 👥 **Role & Permissions** - Fine-grained access control with Spatie Laravel-Permission
- 🎯 **City Weather Pages** - Dynamic city-specific weather monitoring
- 📱 **Modern UI** - Tailwind CSS with responsive design
- 🚀 **TypeScript** - Type-safe frontend development

## 📋 Requirements

- **PHP** 8.4 or higher
- **Laravel** 13
- **Composer** (latest)
- **Node.js** 20.19+ (Vite 8 requirement)
- **pnpm** 10+ (`corepack enable pnpm`)

## 🚀 Quick Start

### Prerequisites

Ensure you have PHP 8.3+, Node.js 20.19+, pnpm, and Composer installed on your system.

### Installation Steps

**1. Clone and install PHP dependencies**

```bash
composer install
```

**2. Install JavaScript dependencies**

```bash
pnpm install
```

**3. Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure your database**
Edit `.env` and set your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weather_app
DB_USERNAME=root
DB_PASSWORD=
```

**5. Run migrations and seeders**

```bash
php artisan migrate --seed
```

This seeds the `admin` and `user` roles plus two demo accounts:

| Role  | Email              | Password   |
| ----- | ------------------ | ---------- |
| Admin | `admin@weather.com` | `password` |
| User  | `user@weather.com`  | `password` |

**6. Start development servers**

Either run everything with one command:

```bash
composer dev   # serve + queue + logs + vite, concurrently
```

…or open two terminal windows:

```bash
# Terminal 1: Laravel server
php artisan serve
```

```bash
# Terminal 2: Vite dev server
pnpm run dev
```

Visit **http://127.0.0.1:8000** in your browser.

## 📦 Production Deployment

**Build assets for production:**

```bash
pnpm run build
```

**Run migrations on production server:**

```bash
php artisan migrate --force
```

**Complete deployment checklist:**

- Set `APP_ENV=production` in `.env`
- Set `APP_DEBUG=false` in `.env`
- Run `php artisan config:cache`
- Run `php artisan route:cache`
- Ensure proper file permissions on `storage/` and `bootstrap/cache/`

## 📝 Notes & Tips

### Development

- The project uses Inertia's script-based initial page payload; ensure `public/hot` points to your Vite dev server
- If TypeScript reports missing declarations for `@inertiajs/vue3`, verify that `resources/js/types/*.d.ts` are included in `tsconfig.json`
- For database reset during development: `php artisan migrate:fresh --seed`

### Troubleshooting

- **CORS Issues**: Check `config/cors.php` if API calls fail
- **Hot Module Replacement not working**: Ensure the Vite dev server is running on `localhost:5173`
- **Permission errors**: Run `php artisan storage:link` for public file access

## 🛠️ Available Commands

```bash
# Laravel
php artisan serve          # Start development server
php artisan tinker         # Interactive shell
php artisan migrate        # Run database migrations
php artisan db:seed        # Run database seeders

# Frontend
pnpm run dev              # Start Vite dev server
pnpm run build            # Build for production
pnpm exec vue-tsc --noEmit  # Type-check the frontend

# Testing
php artisan test          # Run all tests
php artisan test --filter=TestName  # Run specific test
```

## 🗄️ Project Structure

```
├── app/                    # Application code
│   ├── Http/              # Controllers, Requests, Middleware
│   ├── Models/            # Eloquent models (User, City)
│   └── Services/          # Business logic (WeatherService, BmkgService)
├── resources/
│   ├── js/                # Vue components, TypeScript files
│   └── views/             # Blade templates
├── database/
│   ├── migrations/        # Schema migrations
│   └── seeders/          # Database seeders
├── routes/                # Web and API routes
├── config/                # Configuration files
└── tests/                 # Feature and Unit tests
```

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

- Use **PSR-12** coding standards
- Write tests for new features
- Run `php artisan pint` for code formatting (if configured)
- Create descriptive commit messages
- Open a pull request with a clear description

## 📄 License

MIT
