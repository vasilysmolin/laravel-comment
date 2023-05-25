start:
	php artisan serve --host 0.0.0.0 --port 80

test:
	docker-compose exec app-fpm php artisan test

migrate:
	docker-compose exec app-fpm php artisan migrate

autoload:
	docker-compose exec app-fpm composer install

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

lint:
	composer exec phpcs -v

lint-fix:
	composer exec phpcbf -v

phpstan:
	composer exec phpstan analyse

analyse:
	composer exec phpstan analyse -v

config-clear:
	php artisan config:clear

env-prepare:
	cp -n .env.example .env || true

key:
	php artisan key:generate

ide-helper:
	php artisan ide-helper:eloquent
	php artisan ide-helper:gen
	php artisan ide-helper:generate
	php artisan ide-helper:model
	php artisan ide-helper:meta
	php artisan ide-helper:mod -n

update:
	git pull
	docker-compose exec app-fpm composer install --no-interaction --ansi --no-suggest
	docker-compose exec app-fpm php artisan migrate --force
	docker-compose exec app-fpm php artisan optimize

seeder-dev:
	docker-compose exec app-fpm php artisan db:seed


heroku-build:
	php artisan migrate --force
	php artisan db:seed --force
	php artisan optimize

setup:
	composer install
	cp -n .env.example .env|| true
	php artisan key:gen --ansi
	php artisan migrate --force
	php artisan db:seed --force
	php artisan optimize

db-import-from-backup:
	docker-compose exec -T database psql -d tapigo-database -U postgres  < data

setup-ci: env-prepare-ci install-ci key-ci database-prepare-ci seed-ci

env-prepare-ci:
	cp -n .env.example .env || true

key-ci:
	php artisan key:gen --ansi

test-coverage-ci:
	php artisan test --group ci --coverage-clover build/logs/clover.xml

install-ci:
	composer install
	npm i
	npm run build

seed-ci:
	php artisan db:seed --force

optimize-ci:
	php artisan optimize:fresh --seed --force

database-prepare-ci:
	php artisan migrate:fresh --force

build-php:
	docker build -t kitman/php-8.1:$(branch) -f ./images/php/Dockerfile .
	docker push kitman/php-8.1:$(branch)
	docker build -t kitman/nginx:$(branch) -f ./images/nginx/Dockerfile .
	docker push kitman/nginx:$(branch)
