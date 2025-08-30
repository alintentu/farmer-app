#!/bin/bash

set -e

echo "🚀 Deploying Atlas to GitHub"
echo "============================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# Check if git is initialized
check_git() {
    if [ ! -d ".git" ]; then
        print_status "Initializing Git repository..."
        git init
        print_success "Git repository initialized"
    fi
}

# Add remote origin
setup_remote() {
    print_status "Setting up GitHub remote..."
    
    # Check if remote already exists
    if git remote get-url origin > /dev/null 2>&1; then
        print_warning "Remote origin already exists"
        return
    fi
    
    # Add remote origin
    git remote add origin https://github.com/alintentu/atlas.git
    print_success "GitHub remote added"
}

# Create .gitignore
create_gitignore() {
    print_status "Creating .gitignore file..."
    
    cat > .gitignore << 'EOF'
# Laravel
api/.env
api/vendor/
api/node_modules/
api/storage/logs/*
api/storage/framework/cache/*
api/storage/framework/sessions/*
api/storage/framework/views/*
api/bootstrap/cache/*
api/.phpunit.result.cache

# Vue
app/node_modules/
app/dist/
app/.env.local
app/.env.*.local
app/npm-debug.log*
app/yarn-debug.log*
app/yarn-error.log*
app/pnpm-debug.log*
app/lerna-debug.log*

# Docker
.dockerignore

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Logs
*.log
logs/

# Runtime data
pids
*.pid
*.seed
*.pid.lock

# Coverage directory used by tools like istanbul
coverage/
*.lcov

# nyc test coverage
.nyc_output

# Dependency directories
node_modules/
jspm_packages/

# Optional npm cache directory
.npm

# Optional eslint cache
.eslintcache

# Microbundle cache
.rpt2_cache/
.rts2_cache_cjs/
.rts2_cache_es/
.rts2_cache_umd/

# Optional REPL history
.node_repl_history

# Output of 'npm pack'
*.tgz

# Yarn Integrity file
.yarn-integrity

# dotenv environment variables file
.env
.env.test

# parcel-bundler cache (https://parceljs.org/)
.cache
.parcel-cache

# Next.js build output
.next

# Nuxt.js build / generate output
.nuxt
dist

# Gatsby files
.cache/
public

# Storybook build outputs
.out
.storybook-out

# Temporary folders
tmp/
temp/

# Editor directories and files
.vscode/*
!.vscode/extensions.json
.idea
*.suo
*.ntvs*
*.njsproj
*.sln
*.sw?

# Local Netlify folder
.netlify
EOF

    print_success ".gitignore file created"
}

# Add and commit files
commit_files() {
    print_status "Adding files to Git..."
    git add .
    
    print_status "Committing changes..."
    git commit -m "Initial commit: Atlas SaaS Platform

- Laravel 11 backend with DDD architecture
- Vue 3 frontend with TypeScript
- Multi-tenant SaaS platform
- Modular design with feature gates
- Docker containerization
- Comprehensive testing setup
- Enterprise-grade code quality tools"
    
    print_success "Files committed"
}

# Push to GitHub
push_to_github() {
    print_status "Pushing to GitHub..."
    
    # Check if main branch exists
    if git branch --list | grep -q "main"; then
        git push -u origin main
    else
        # Create main branch and push
        git checkout -b main
        git push -u origin main
    fi
    
    print_success "Code pushed to GitHub"
}

# Main execution
main() {
    print_status "Starting GitHub deployment..."
    
    check_git
    create_gitignore
    setup_remote
    commit_files
    push_to_github
    
    print_success "🎉 Atlas successfully deployed to GitHub!"
    echo ""
    echo "Repository: https://github.com/alintentu/atlas"
    echo ""
    echo "Next steps:"
    echo "  1. Clone the repository on other machines"
    echo "  2. Run 'make init' for first-time setup"
    echo "  3. Run 'make setup' to start development"
    echo "  4. Set up GitHub Actions for CI/CD"
}

# Run main function
main "$@"
