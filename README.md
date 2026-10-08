# VELOS Automotive Point of Sale

A CodeIgniter 4 point-of-sale system for a car showroom. Staff can manage cars, customers, and team members; record a sale; and review sales history. The interface is responsive and uses original generated automotive imagery.

**Live site:** [velos-pos.vercel.app](https://velos-pos.vercel.app/login)

## Features

- Staff sign-in and sign-out with hashed passwords and database-backed sessions
- Protected management routes and CSRF-protected forms
- Create, view, edit, and archive cars, customers, and staff
- Verified JPEG, PNG, and WebP uploads, stored in the database and served with safe image headers
- Record a sale with an optional customer, current product price, and an atomic stock update
- Reject sales that exceed available stock without creating a transaction
- Sales history with product, customer, staff, quantity, total, and date
- Dashboard with revenue, counts, recent sales, and low-stock notices

Archived records are soft-deleted so historical sales retain their relationships. The migration adds `deleted_at` and `updated_at` fields to the brief's core schema, plus `media` and `ci_sessions` tables for persistent uploads and sessions. It supports MySQL/MariaDB locally and PostgreSQL for a Vercel-hosted Neon database.

## Requirements

- PHP 8.2 or newer with `intl`, `mbstring`, `fileinfo`, and the driver for your database (`mysqli` or `pgsql`)
- Composer
- MySQL/MariaDB or PostgreSQL

## Local setup

1. Clone the repository and run `composer install`.
2. Create an empty MySQL database named `velos_pos`.
3. Copy `env` to `.env`. Set `CI_ENVIRONMENT = development`, `app.baseURL = 'http://localhost:8080/'`, and the `database.default.*` MySQL connection fields.
4. Run `php spark migrate --all`.
5. Set `SEED_ADMIN_PASSWORD` in your terminal to the password provided separately for the project, then run `php spark db:seed InitialSeeder`. The seeder creates username `SirVon`, hashes the supplied password, and adds three sample cars with images. The password is never stored in the repository.
6. Run `php spark serve --host localhost --port 8080` and open `http://localhost:8080/login`.

For PowerShell, the seed command can be run with:

```powershell
$env:SEED_ADMIN_PASSWORD = '<your project password>'
php spark db:seed InitialSeeder
Remove-Item Env:SEED_ADMIN_PASSWORD
```

## Vercel deployment

The project includes `api/index.php` and `vercel.json` for the [community PHP runtime](https://github.com/vercel-community/php). Vercel's PHP runtime is community maintained. A persistent external database is required because Vercel Functions do not provide persistent local storage. The app stores sessions and uploaded images in the database; temporary framework files use `/tmp` on Vercel.

The live deployment uses the Vercel project `velos-pos` in the `elishas-projects-c8707e7b` team with its connected Neon PostgreSQL database. Its Production environment has `APP_BASE_URL=https://velos-pos.vercel.app/`. The initial migration and seed completed on the first build; later builds run them idempotently without `SEED_ADMIN_PASSWORD`.

1. Provision a PostgreSQL database through Neon or a MySQL database that accepts connections from Vercel Functions.
2. Connect the database to the Vercel project, or set the MySQL connection variables below.
3. On the first production build, provide `SEED_ADMIN_PASSWORD` as a temporary build-time variable with the separately provided project password. The Composer `vercel` hook runs migrations and seeds the first account and sample cars. Remove that variable after the first successful build; later builds run the idempotent seeder without it.
4. Import this GitHub repository into Vercel or run `vercel --prod` from the project root.
5. In Vercel project settings, add these environment variables for Production:

| Variable | Value |
| --- | --- |
| `CI_ENVIRONMENT` | `production` |
| `POSTGRES_URL_NON_POOLING`, `DATABASE_URL_UNPOOLED`, `POSTGRES_URL`, or `DATABASE_URL` | PostgreSQL URL supplied by Neon. Direct connections are preferred for database-backed sessions. Alternatively use the `POS_DB_*` MySQL variables below. |
| `POS_DB_HOST` | MySQL hostname, when using MySQL |
| `POS_DB_NAME` | MySQL database name |
| `POS_DB_USER` | MySQL username |
| `POS_DB_PASSWORD` | MySQL password |
| `POS_DB_PORT` | MySQL port, usually `3306` |
| `APP_BASE_URL` | Final HTTPS site URL, ending in `/` |

The application uses `VERCEL_URL` when `APP_BASE_URL` is absent, but a stable production URL is preferable. Never commit `.env`, database credentials, or the seed password. `vendor/` is excluded from Git and installed by Composer during the Vercel PHP build.

## Structure

| Path | Purpose |
| --- | --- |
| `app/Controllers` | Authentication, dashboard, CRUD, sales, and media responses |
| `app/Models` | CodeIgniter data models |
| `app/Views` | Responsive staff interface |
| `app/Database/Migrations` | Relational schema and supporting tables |
| `app/Database/Seeds` | Initial staff account and sample inventory |
| `app/Filters/AuthFilter.php` | Management-page access control |
| `app/Libraries/Uploads.php` | Image validation and persistent storage |
| `public` | Static design assets and local front controller |

## Security and data behavior

The app validates required fields and uploaded image type, size, and dimensions. It hashes staff passwords, regenerates the session after sign-in, checks the logged-in user on protected routes, and uses CSRF tokens on mutations. Sale recording uses a conditional SQL update and a transaction. If stock is insufficient or insertion fails, the transaction rolls back.

Prices and labels in the sample catalog are demonstration data. Change them in Inventory before real use.
