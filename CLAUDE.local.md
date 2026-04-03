# Local Docker Development

This project runs in Docker containers. All commands must be executed through `docker compose exec` in the appropriate container.

## Command Routing

- **PHP / Composer / Artisan / Pint**: Use the `php-fpm` container
  - `docker compose exec php-fpm composer ...`
  - `docker compose exec php-fpm php artisan ...`
  - `docker compose exec php-fpm vendor/bin/pint ...`
- **NPM**: Use the `node` container
  - `docker compose exec node npm ...`
  - `docker compose exec node npx ...`

## Examples

```bash
# Running tests
docker compose exec php-fpm php artisan test --compact

# Installing composer dependencies
docker compose exec php-fpm composer require some/package

# Running Pint
docker compose exec php-fpm vendor/bin/pint --dirty --format agent

# Building frontend assets
docker compose exec node npm run build

# Running ESLint
docker compose exec node npx eslint ...

Do NOT run `php`, `composer`, `npm`, `npx`, or `vendor/bin/pint` directly on the host.
