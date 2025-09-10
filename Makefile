# Atlas SaaS Platform - Development Commands

.PHONY: help install setup up down logs clean reset migrate seed test lint format

# Default target
help:
	@echo "Atlas SaaS Platform - Available Commands:"
	@echo ""
	@echo "Setup & Installation:"
	@echo "  install    - Install all dependencies (Laravel + Vue)"
	@echo "  setup      - Complete project setup (install + migrate + seed)"
	@echo ""
	@echo "Docker Management:"
	@echo "  up         - Start all containers"
	@echo "  down       - Stop all containers"
	@echo "  logs       - Show container logs"
	@echo "  clean      - Remove containers and volumes"
	@echo ""
	@echo "Database:"
	@echo "  migrate    - Run database migrations"
	@echo "  seed       - Seed database with initial data"
	@echo "  reset      - Reset database (migrate:fresh + seed)"
	@echo ""
	@echo "Development:"
	@echo "  test       - Run tests"
	@echo "  lint       - Run linting"
	@echo "  format     - Format code"
	@echo "  tinker     - Open Laravel Tinker"

# Installation
install:
	@echo "Installing Laravel dependencies..."
	docker compose run --rm php composer install
	@echo "Installing Vue dependencies..."
	docker compose run --rm node npm install
	@echo "Installation complete!"

# Initialize project (first time setup)
init:
	@echo "Running initialization script..."
	./scripts/init.sh

# Deploy to GitHub
deploy:
	@echo "Deploying to GitHub..."
	./scripts/deploy-to-github.sh

# Complete setup
setup: install
	@echo "Setting up environment..."
	cp api/env.example api/.env
	docker compose run --rm php php artisan key:generate
	@echo "Starting containers..."
	docker compose up -d
	@echo "Waiting for database..."
	sleep 10
	@echo "Running migrations..."
	docker compose exec php php artisan migrate --force
	@echo "Seeding database..."
	docker compose exec php php artisan db:seed --class=AtlasSeeder --force
	@echo "Setup complete! 🚀"
	@echo ""
	@echo "Access your applications:"
	@echo "  Frontend: http://localhost:5173"
	@echo "  API: http://localhost:8081"
	@echo "  Meilisearch: http://localhost:7700"
	@echo "  MailHog: http://localhost:8025"

# Docker commands
up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f --tail=100

clean:
	docker compose down -v --remove-orphans
	docker system prune -f

# Database commands
migrate:
	docker compose exec php php artisan migrate --force

seed:
	docker compose exec php php artisan db:seed --class=AtlasSeeder --force

reset:
	docker compose exec php php artisan migrate:fresh --seed --force

# Development commands
test:
	docker compose exec php php artisan test
	docker compose run --rm node npm run test:unit

lint:
	docker compose exec php composer run analyse
	docker compose run --rm node npm run lint

format:
	docker compose exec php composer run format
	docker compose run --rm node npm run format

tinker:
	docker compose exec php php artisan tinker

# Quick development helpers
dev:
	docker compose up -d
	@echo "Development environment started!"
	@echo "Frontend: http://localhost:5173"
	@echo "API: http://localhost:8081"

restart:
	docker compose restart

rebuild:
	docker compose down
	docker compose build --no-cache
	docker compose up -d
