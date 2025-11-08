up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

dbfresh:
	docker-compose exec -it php-fpm sh -c "php artisan migrate:fresh --seed && php artisan app:refresh-test-data"

npminstall:
	docker-compose run --rm node sh -c "npm install"

vite:
	docker-compose run -p 5173:5173 --rm node sh -c "npm run dev"

c ?= install
composer: ## Run composer install (optional c=SPECIFIC_PACKAGE)
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "composer ${c}"

pint:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "./vendor/bin/pint --dirty"

.PHONY: artisan
a ?= about
artisan:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan ${a}"

tinker:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh -c "php artisan tinker"

shell:
	docker-compose exec -u $$(id -u):$$(id -g) php-fpm sh
