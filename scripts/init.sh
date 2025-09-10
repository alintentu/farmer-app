#!/bin/bash

set -e

echo "🚀 Atlas SaaS Platform - Initialization Script"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is running
check_docker() {
    print_status "Checking Docker..."
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker and try again."
        exit 1
    fi
    print_success "Docker is running"
}

# Initialize Laravel project
init_laravel() {
    print_status "Initializing Laravel project..."
    
    if [ ! -d "api" ]; then
        print_status "Creating Laravel project..."
        docker compose run --rm php composer create-project laravel/laravel:^11.0 . --prefer-dist
        print_success "Laravel project created"
    else
        print_warning "Laravel project already exists, skipping creation"
    fi
    
    # Copy environment file
    if [ ! -f "api/.env" ]; then
        print_status "Setting up environment file..."
        cp api/env.example api/.env
        print_success "Environment file created"
    fi
    
    # Install dependencies
    print_status "Installing Laravel dependencies..."
    docker compose run --rm php composer install
    print_success "Laravel dependencies installed"
    
    # Generate application key
    print_status "Generating application key..."
    docker compose run --rm php php artisan key:generate
    print_success "Application key generated"
}

# Initialize Vue project
init_vue() {
    print_status "Initializing Vue project..."
    
    if [ ! -d "app" ]; then
        print_status "Creating Vue project..."
        docker compose run --rm node npm create vue@latest . -- --ts --router --pinia --vitest --eslint-with-prettier --yes
        print_success "Vue project created"
    else
        print_warning "Vue project already exists, skipping creation"
    fi
    
    # Install dependencies
    print_status "Installing Vue dependencies..."
    docker compose run --rm node npm install
    print_success "Vue dependencies installed"
    
    # Install additional packages
    print_status "Installing additional Vue packages..."
    docker compose run --rm node npm install axios vue-i18n@9 @headlessui/vue @heroicons/vue @vueuse/core date-fns lodash-es vue-toastification
    print_success "Additional packages installed"
}

# Setup database
setup_database() {
    print_status "Setting up database..."
    
    # Start containers
    print_status "Starting containers..."
    docker compose up -d db redis meili
    
    # Wait for database to be ready
    print_status "Waiting for database to be ready..."
    sleep 10
    
    # Run migrations
    print_status "Running database migrations..."
    docker compose exec php php artisan migrate --force
    print_success "Database migrations completed"
    
    # Seed database
    print_status "Seeding database..."
    docker compose exec php php artisan db:seed --class=AtlasSeeder --force
    print_success "Database seeded"
}

# Install Laravel packages
install_laravel_packages() {
    print_status "Installing Laravel packages..."
    
    docker compose exec php composer require \
        stancl/tenancy:^4 \
        laravel/scout \
        meilisearch/meilisearch-php \
        laravel/sanctum \
        laravel/cashier \
        spatie/laravel-permission \
        spatie/laravel-activitylog \
        spatie/laravel-backup \
        spatie/laravel-query-builder \
        spatie/laravel-ray \
        barryvdh/laravel-debugbar \
        nunomaduro/collision \
        nunomaduro/larastan \
        phpunit/phpunit \
        pestphp/pest \
        pestphp/pest-plugin-laravel \
        fakerphp/faker \
        guzzlehttp/guzzle
    
    docker compose exec php composer require --dev \
        laravel/pint \
        laravel/telescope \
        nunomaduro/phpinsights
    
    print_success "Laravel packages installed"
    
    # Publish package configurations
    print_status "Publishing package configurations..."
    docker compose exec php php artisan vendor:publish --tag=tenancy
    docker compose exec php php artisan vendor:publish --tag=sanctum-config
    print_success "Package configurations published"
}

# Main execution
main() {
    print_status "Starting Atlas initialization..."
    
    check_docker
    init_laravel
    init_vue
    install_laravel_packages
    setup_database
    
    print_success "🎉 Atlas initialization completed!"
    echo ""
    echo "Access your applications:"
    echo "  Frontend: http://localhost:5173"
    echo "  API: http://localhost:8081"
    echo "  Meilisearch: http://localhost:7700"
    echo "  MailHog: http://localhost:8025"
    echo ""
    echo "Demo credentials:"
    echo "  Email: demo@atlas.com"
    echo "  Password: password"
    echo ""
    echo "Next steps:"
    echo "  1. Start all containers: make up"
    echo "  2. View logs: make logs"
    echo "  3. Run tests: make test"
    echo "  4. Format code: make format"
}

# Run main function
main "$@"
