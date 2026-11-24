# Deployment Guide

This document outlines the production deployment checklist, environment configuration, secrets handling, logging setup, and rollback procedures for the **Mini Workshop Reservation Application**.

## Environment Variables

Configure the following key variables in your production `.env` file (or directly in your server/hosting platform's environment settings):

| Variable | Recommended Value | Purpose |
| :--- | :--- | :--- |
| `APP_ENV` | `production` | Sets the application environment to production, disabling local-only behaviors. |
| `APP_KEY` | `base64:...` | 32-byte encryption key used for sessions, CSRF tokens, and encrypted cookies. Generate with `php artisan key:generate`. |
| `APP_DEBUG` | `false` | **Must be `false` in production** to prevent exposing stack traces, SQL queries, and environment values to end users. |
| `APP_URL` | `https://your-domain.com` | Base URL used by Laravel when generating absolute links, redirects, and asset URLs. |
| `DB_CONNECTION` | `mysql` | Database driver (`mysql` for production). |
| `DB_HOST` | `127.0.0.1` (or DB host) | Hostname or IP address of the production database server. |
| `DB_PORT` | `3306` | Database server port. |
| `DB_DATABASE` | `workshop_reservation` | Name of the production database. |
| `DB_USERNAME` | `<db-user>` | Database user with read/write/schema privileges for the application database. |
| `DB_PASSWORD` | `<strong-password>` | Strong password for the production database user. |

### Production Optimization Commands
After updating environment variables or deploying new code, cache configuration and routes for optimal performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Migrations

Run database migrations during deployment using the `--force` flag:

```bash
php artisan migrate --force
```

- **Why `--force` is required:** When `APP_ENV=production`, Laravel blocks database migrations by default and prompts for interactive confirmation to prevent accidental schema changes. Passing `--force` allows migrations to run non-interactively in automated deployment pipelines.
- **Note:** Do not run `php artisan migrate:fresh` or `db:seed` with test fixtures on a live production database.

## Logging

- **Default Behavior:** By default, Laravel writes application logs to `storage/logs/laravel.log`. Ensure the web server user (`www-data` or equivalent) has write permissions to `storage/` and `bootstrap/cache/`.
- **Production Channel Recommendations:**
  - **Traditional VM / VPS:** Set `LOG_CHANNEL=stack` (with `LOG_STACK=daily` in `.env`) to rotate log files daily and avoid unbounded single-file growth.
  - **Container / Docker / Cloud PaaS:** Set `LOG_CHANNEL=stderr` so logs stream directly to standard error (`stderr`) for collection by Docker, Kubernetes, or cloud log aggregators.
- **Log Level:** Set `LOG_LEVEL=error` or `LOG_LEVEL=warning` in production to reduce log noise.

## Secrets Management

- **Never commit `.env` to version control:** Ensure `.env` remains listed in `.gitignore` and store only `.env.example` (with placeholder values) in Git.
- **Use External Secret Stores:** In production, inject sensitive credentials (`APP_KEY`, `DB_PASSWORD`, API keys) via:
  - Server-level environment variables,
  - Your hosting platform's encrypted environment variable settings, or
  - A dedicated secrets manager such as **AWS Secrets Manager** or **HashiCorp Vault**.
- **Protect and Preserve `APP_KEY`:** Keep `APP_KEY` strictly confidential and **consistent across deployments and server instances**. Rotating or losing `APP_KEY` will invalidate all active user sessions and render previously encrypted data unreadable.

## Rollback Steps

If a deployment introduces an application error or failed migration, follow these steps immediately:

1. **Revert to the previous stable code version:**
   - Using Git:
     ```bash
     git checkout <previous-stable-tag-or-commit>
     composer install --no-dev --optimize-autoloader
     ```
   - Or trigger a rollback to the previous release via your deployment tool / hosting dashboard.

2. **Roll back the failed database migration (if the issue was caused by a migration):**
   ```bash
   php artisan migrate:rollback --force
   ```

3. **Clear all stale application caches:**
   ```bash
   php artisan config:clear && php artisan cache:clear && php artisan route:clear
   ```
   *(Optionally rebuild clean production caches afterward with `php artisan config:cache && php artisan route:cache`.)*

4. **Verify service health:**
   Confirm the application and database connection are healthy by checking `GET /health` (`{"status":"ok"}` with HTTP `200`).