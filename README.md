# Mercedes-Benz Showroom Point of Sale

A CodeIgniter 4 point-of-sale system for a Mercedes-Benz themed car showroom. Staff can manage cars, customers, and team members; record a sale; and review sales history. This is an unofficial student project and is not affiliated with Mercedes-Benz Group AG.

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
- Fifteen sample Mercedes-Benz car listings with model-matched photographs, category filters, live search, price sorting, and rotatable 3D previews of the real Mercedes model families

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
5. Set `SEED_ADMIN_PASSWORD` in your terminal to the password provided separately for the project, then run `php spark db:seed InitialSeeder`. The migrations add fifteen Mercedes-Benz demo listings and attach photographs to twelve of them; the seeder creates username `SirVon`, hashes the supplied password, and attaches photos to the other three listings. The password is never stored in the repository.
6. Run `php spark serve --host localhost --port 8080` and open `http://localhost:8080/login`.

For PowerShell, the seed command can be run with:

```powershell
$env:SEED_ADMIN_PASSWORD = '<your project password>'
php spark db:seed InitialSeeder
Remove-Item Env:SEED_ADMIN_PASSWORD
```

## Vercel deployment

The project includes `api/index.php` and `vercel.json` for the [community PHP runtime](https://github.com/vercel-community/php). Vercel's PHP runtime is community maintained. A persistent external database is required because Vercel Functions do not provide persistent local storage. The app stores sessions and uploaded images in the database; temporary framework files use `/tmp` on Vercel.

The live deployment uses the Vercel project `velos-pos` in the `elishas-projects-c8707e7b` team with its connected Neon PostgreSQL database. Its Production environment has `APP_BASE_URL=https://velos-pos.vercel.app/`. The initial migration and seed completed on the first build; later builds run them idempotently without `SEED_ADMIN_PASSWORD`. A later migration renames only the original fictional demo models and replaces their images only when the stored image still matches the initial seed. Another migration adds the remaining demo models by name without modifying staff-entered listings, stock, prices, customers, or sales.

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

Prices and labels in the sample catalog are demonstration data. Change them in Inventory before real use. The 360° previews embed independent 3D recreations of the named Mercedes-Benz model families from Sketchfab. They are not photographs, manufacturer configurator renders, or scans of the exact inventory vehicles; model year, trim, and color may differ from the listing photo. The embed loads only when a visitor opens a preview, and its creator is credited next to the viewer. Staff-added cars without a matched model keep their listing photo and do not show a 360° button.

## Image credits

The sign-in and original three vehicle photos are by [Isaac Mitchell](https://www.pexels.com/photo/black-mercedes-benz-car-15171354/), [Mohit Hambiria](https://www.pexels.com/photo/new-mercedes-benz-in-showroom-20123634/), [Muhammet Raşit Kaplan](https://www.pexels.com/photo/white-mercedes-e-class-17233277/), and [Mike Bird](https://www.pexels.com/photo/black-mercedes-g-class-15610300/). Additional model photos via [Pexels](https://www.pexels.com/license/): [C-Class by Vitali Adutskevich](https://www.pexels.com/photo/black-mercedes-c-class-16284837/), [A-Class by Mike Bird](https://www.pexels.com/photo/mercedes-benz-a-class-hatchback-with-open-doors-31292760/), [CLA by Mike Bird](https://www.pexels.com/photo/white-mercedes-benz-cla-180-car-16495911/), [CLE by Molnár Tamás Photography](https://www.pexels.com/photo/white-mercedes-benz-cle-at-a-public-show-25634577/), [GLA by Mike Bird](https://www.pexels.com/photo/parked-white-mercedes-benz-gla-16714056/), [GLB by Batuhan Kocabaş](https://www.pexels.com/photo/mercedes-glb-200-17887805/), [GLC by Mike Bird](https://www.pexels.com/photo/red-mercedes-benz-glc-in-car-showroom-40084253/), [GLE by Mike Bird](https://www.pexels.com/photo/black-mercedes-benz-gle-on-a-parking-lot-14692379/), [GLS by Dextar Studio](https://www.pexels.com/photo/mercedes-amg-gls-63-at-a-car-dealership-18311312/), [EQE by Mike Bird](https://www.pexels.com/photo/sleek-black-mercedes-benz-eqe-on-outdoor-display-29830328/), [EQS by 04iraq](https://www.pexels.com/photo/mercedes-benz-eqs-electric-car-at-sunset-29779242/), and [AMG GT by Jacob Moore](https://www.pexels.com/photo/mercedes-amg-gt-16124128/). The three-pointed star asset is from [Wikimedia Commons](https://commons.wikimedia.org/wiki/File:Mercedes-Benz_Star_2022.svg). Mercedes-Benz names and the star are trademarks of their respective owner.

## 360° model credits

The models remain hosted on Sketchfab and appear through its public viewer. Each model's page gives its creator and license; the [Sketchfab embedding guidance](https://sketchfab.com/press) permits embedding public models. The CLA model uses CC BY-SA 4.0; the other listed models use CC BY 4.0. No 3D asset files are redistributed by this repository.

| Car | Model and creator |
| --- | --- |
| S-Class | [Mercedes S-class by SipanBareyan](https://sketchfab.com/models/8095e37cc664438d9e0995481280da97) |
| E-Class | [Mercedes E Class W212 by Peter_D](https://sketchfab.com/models/119c5e10733142b197aa53b86f6aeb04) |
| G-Class | [Mercedes G-class by Lexyc16](https://sketchfab.com/models/1a2a52b16cad4e618af347461817895c) |
| C-Class | [2022 Mercedes-Benz C-Class by tonielpro520](https://sketchfab.com/models/5e1a44c940234b37aa0ffddf53e5f296) |
| A-Class | [Mercedes-Benz A-Class W169 by Merc_TV](https://sketchfab.com/models/842e5669e54b49debebfa7b7b5ae2c15) |
| CLA | [Mercedes Benz CLA by RADMATTER12](https://sketchfab.com/models/5fb2ada60d8b488dacf16a064fbbbb16) |
| CLE | [Mercedes-Benz CLE by Nieve5677](https://sketchfab.com/models/c857767d5cc2498bb4c1dca111f554e3) |
| GLA | [Mercedes-Benz GLA-Class 2020 by ItsDiyor](https://sketchfab.com/models/ab7b5df3ba634f38a14effde3d542466) |
| GLB | [Mercedes GLB AMG by RADMATTER12](https://sketchfab.com/models/8e5418418908427b997e429e02dfafc4) |
| GLC | [Mercedes-Benz GLC coupe by amogusstrikesback2](https://sketchfab.com/models/a2f1906aad304b2e99e657d2c82146dc) |
| GLE | [Mercedes-Benz GLE-class W166 by Savelliy 07](https://sketchfab.com/models/1038fbd56eef423cbfd9370008d7dd8f) |
| GLS | [Mercedes Benz GLS 450 by Es-star kings](https://sketchfab.com/models/7e2acb615d8f4d35bf3708acdd10f0b9) |
| EQE | [Mercedes Benz EQE by Nieve5677](https://sketchfab.com/models/8c87a3c2e7444efbb671eefff1272632) |
| EQS | [Mercedes Benz EQS by ixumix.sds](https://sketchfab.com/models/7be15f3c83534c7698dc78e1c25e7562) |
| AMG GT | [Mercedes Benz AMG GT by Yan Carvalho](https://sketchfab.com/models/661dcab94455463784651a3ebc63cfb9) |
