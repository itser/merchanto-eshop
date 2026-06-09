SAIL := ./vendor/bin/sail

.DEFAULT_GOAL := help

.PHONY: help \
	up down restart ps shell logs \
	artisan composer migrate fresh db-show \
	test test-catalog test-order pest \
	pint duster duster-fix stan check \
	npm npm-dev build

# ------------------------------------------------------------------------------
# Sail / Docker
# ------------------------------------------------------------------------------

help: ## Show available commands
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

up: ## Start Sail containers
	$(SAIL) up -d

down: ## Stop Sail containers
	$(SAIL) down

restart: down up ## Restart Sail containers

ps: ## Show container status
	$(SAIL) ps

shell: ## Open shell in app container
	$(SAIL) shell

logs: ## Tail app container logs
	$(SAIL) logs -f laravel.test

# ------------------------------------------------------------------------------
# Application
# ------------------------------------------------------------------------------

artisan: ## Run artisan (usage: make artisan cmd="route:list")
	$(SAIL) artisan $(cmd)

composer: ## Run composer (usage: make composer cmd="install")
	$(SAIL) composer $(cmd)

migrate: ## Run database migrations
	$(SAIL) artisan migrate

fresh: ## Drop all tables and re-run migrations
	$(SAIL) artisan migrate:fresh

db-show: ## Show database connection info
	$(SAIL) artisan db:show

# ------------------------------------------------------------------------------
# Tests (Pest / PHPUnit via Sail)
# ------------------------------------------------------------------------------

test: ## Run all tests
	$(SAIL) artisan test

test-catalog: ## Run Catalog module tests
	$(SAIL) artisan test Modules/Catalog/tests

test-order: ## Run Order module tests
	$(SAIL) artisan test Modules/Order/tests

pest: ## Run Pest directly
	$(SAIL) php vendor/bin/pest

# ------------------------------------------------------------------------------
# Code quality
# ------------------------------------------------------------------------------

pint: ## Fix code style (Laravel Pint)
	$(SAIL) bin pint

duster: ## Lint code style (Laravel Duster)
	$(SAIL) php vendor/bin/duster lint

duster-fix: ## Fix code style (Laravel Duster)
	$(SAIL) php vendor/bin/duster fix

stan: ## Run static analysis (Larastan)
	$(SAIL) php vendor/bin/phpstan analyse

check: test pint duster stan ## Run tests and all quality checks

# ------------------------------------------------------------------------------
# Frontend (Vite)
# ------------------------------------------------------------------------------

npm: ## Run npm (usage: make npm cmd="install")
	$(SAIL) npm $(cmd)

npm-dev: ## Start Vite dev server
	$(SAIL) npm run dev

build: ## Build frontend assets
	$(SAIL) npm run build
