# Docker Compose API
up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

# php-fpm container
c ?= install
composer:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "composer ${c}"

.PHONY: artisan
a ?= about
artisan:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan ${a}"

dbfresh:
	docker-compose exec -it php-fpm sh -c "php artisan migrate:fresh --seed && php artisan app:refresh-test-data"

tinker:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan tinker"

pint:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "./vendor/bin/pint --dirty"

shell:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh

# nodejs container
n ?= help
npm:
	docker-compose run --rm node sh -c "npm ${n}"

vite:
	docker-compose run -p 5173:5173 --rm node sh -c "npm run dev"

# redis container
redis:
	docker-compose exec -it redis sh
