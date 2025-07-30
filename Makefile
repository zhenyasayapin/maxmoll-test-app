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