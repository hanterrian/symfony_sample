ARGUMENTS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

# Start containers
up:
	docker compose up -d

# Stop containers
down:
	docker compose down --remove-orphans

# Build containers
build:
	docker compose build --no-cache

php:
	docker compose exec -u 1000 php sh

php-root:
	docker compose exec php sh

composer:
	docker compose exec -u 1000 php composer $(ARGUMENTS)

console:
	docker compose exec -u 1000 php php bin/console $(ARGUMENTS)
