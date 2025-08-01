PHP_SERVICE=php
ARGS=$(filter-out $@,$(MAKECMDGOALS))

restart:
	@docker compose down
	@docker compose up -d
build:
	@docker compose build
up:
	docker compose up -d
symfony:
	@docker compose exec ${PHP_SERVICE} symfony $(ARGS)
console:
	@docker compose exec ${PHP_SERVICE} symfony console $(ARGS)
bash:
	@docker compose exec ${PHP_SERVICE} bash $(ARGS)
composer:
	@docker compose exec ${PHP_SERVICE} composer $(ARGS)
phpunit-filter:
	$(MAKE) phpunit-prepare
	@docker compose exec ${PHP_SERVICE} vendor/bin/phpunit --filter $(ARGS)
phpunit-prepare:
	@docker compose exec ${PHP_SERVICE} symfony console d:d:d --force --no-interaction --env=test --if-exists
	@docker compose exec ${PHP_SERVICE} symfony console d:d:c --no-interaction --env=test
	@docker compose exec ${PHP_SERVICE} symfony console d:m:m --no-interaction --env=test
fixtures:
	@docker compose exec ${PHP_SERVICE} symfony console d:f:l --no-interaction