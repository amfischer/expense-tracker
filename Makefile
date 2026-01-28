# Docker Compose API
up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

# php-fpm container
c ?= list
composer:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "composer ${c}"

.PHONY: artisan
a ?= about
artisan:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan ${a}"

tinker:
	docker-compose exec -it -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan tinker"

shell:
	docker-compose exec -it -u $$(id -u):$$(id -g) php-fpm sh

dbfresh:
	docker-compose exec php-fpm sh -c "php artisan migrate:fresh --seed && php artisan app:refresh-test-data"

pint:
	docker-compose exec php-fpm sh -c "./vendor/bin/pint"

## tests
f ?=
test-feature:
	docker-compose exec -it php-fpm sh -c "php artisan test tests/Feature/${f}"

u ?=
test-unit:
	docker-compose exec -it php-fpm sh -c "php artisan test tests/Unit/${u}"

test-all:
	docker-compose exec -it php-fpm sh -c "php artisan test"

# nodejs container
n ?= help
npm:
	docker-compose run --rm node sh -c "npm ${n}"

vite:
	docker-compose run -p 5173:5173 --rm node sh -c "npm run dev"

# redis container
redis:
	docker-compose exec -it redis sh
