# Atlas - Modern SaaS Platform

A comprehensive, multi-tenant SaaS platform built with Laravel 11 and Vue 3, featuring modular architecture and enterprise-grade standards.

## 🚀 Features

- **Multi-tenant Architecture** - Isolated tenant environments with feature gates
- **Modular Design** - Plug-and-play modules (Tasks, CRM, Invoicing, etc.)
- **Modern Tech Stack** - Laravel 11 + Vue 3 + TypeScript + Tailwind CSS
- **Enterprise Standards** - DDD, Clean Architecture, comprehensive testing
- **Real-time Features** - WebSocket support, live updates
- **Advanced Search** - Meilisearch integration
- **Payment Processing** - Stripe integration with subscription management
- **Comprehensive Logging** - Activity logs, audit trails
- **API-First Design** - RESTful API with comprehensive documentation

## 🏗️ Architecture

### Backend (Laravel 11)
- **Domain-Driven Design (DDD)** - Clean separation of concerns
- **Multi-tenancy** - Tenant isolation with feature gates
- **API Resources** - RESTful API with proper HTTP status codes
- **Authentication** - Laravel Sanctum with role-based access
- **Database** - MySQL 8.0 with proper indexing and relationships
- **Caching** - Redis for session and cache management
- **Search** - Meilisearch for fast, typo-tolerant search
- **Testing** - Pest PHP with comprehensive test coverage

### Frontend (Vue 3)
- **Composition API** - Modern Vue 3 with TypeScript
- **State Management** - Pinia for reactive state management
- **Routing** - Vue Router with navigation guards
- **UI Components** - Headless UI + Heroicons
- **Styling** - Tailwind CSS with custom design system
- **Type Safety** - Strict TypeScript configuration
- **Testing** - Vitest for unit testing

## 📦 Modules

| Module | Description | Features |
|--------|-------------|----------|
| **Tasks** | Project & task management | Kanban boards, time tracking, Gantt charts |
| **CRM** | Customer relationship management | Contacts, leads, deals, pipelines |
| **Invoicing** | Billing & expense tracking | Invoices, expenses, recurring billing |
| **Marketing** | Email marketing & campaigns | Email campaigns, landing pages, analytics |
| **Automation** | Workflow automation | Webhooks, integrations, API access |
| **Analytics** | Advanced reporting | Custom reports, data export, real-time analytics |
| **Docs** | Documentation & wiki | Versioning, collaboration, search |
| **Helpdesk** | Customer support | Ticket management, knowledge base, surveys |

## 🛠️ Tech Stack

### Backend
- **PHP 8.3** - Latest PHP with strict typing
- **Laravel 11** - Modern PHP framework
- **MySQL 8.0** - Reliable database
- **Redis 7** - Caching and sessions
- **Meilisearch** - Fast search engine
- **Docker** - Containerized development

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript
- **Vite** - Fast build tool
- **Tailwind CSS** - Utility-first CSS framework
- **Pinia** - State management
- **Vue Router** - Client-side routing

### DevOps
- **Docker Compose** - Multi-container orchestration
- **GitHub Actions** - CI/CD pipeline
- **PHPStan** - Static analysis
- **ESLint** - Code linting
- **Prettier** - Code formatting

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/alintentu/atlas.git
   cd atlas
   ```

2. **Complete setup**
   ```bash
   make setup
   ```

   This command will:
   - Install all dependencies (Laravel + Vue)
   - Set up environment files
   - Start all containers
   - Run database migrations
   - Seed initial data

3. **Access the applications**
   - **Frontend**: http://localhost:5173
   - **API**: http://localhost:8081
   - **Meilisearch**: http://localhost:7700
   - **MailHog**: http://localhost:8025

### Demo Credentials
- **Email**: demo@atlas.com
- **Password**: password

## 📋 Available Commands

```bash
# Setup & Installation
make install          # Install dependencies
make setup           # Complete project setup

# Docker Management
make up              # Start containers
make down            # Stop containers
make logs            # Show logs
make clean           # Clean containers and volumes

# Database
make migrate         # Run migrations
make seed            # Seed database
make reset           # Reset database

# Development
make test            # Run tests
make lint            # Run linting
make format          # Format code
make tinker          # Laravel Tinker
```

## 🏗️ Project Structure

```
atlas/
├── api/                    # Laravel Backend
│   ├── app/
│   │   ├── Domain/         # Domain layer (DDD)
│   │   │   ├── User/
│   │   │   ├── Tenant/
│   │   │   └── Module/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Middleware/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
├── app/                    # Vue Frontend
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   ├── services/
│   │   └── types/
│   └── public/
├── docker/                 # Docker configuration
├── docker-compose.yml      # Container orchestration
└── Makefile               # Development commands
```

## 🔧 Configuration

### Environment Variables

Copy the example environment file and configure your settings:

```bash
cp api/env.example api/.env
```

Key configuration options:
- Database credentials
- Redis settings
- Meilisearch configuration
- Stripe API keys
- Feature flags

### Feature Flags

Control module availability per tenant:

```env
FEATURE_TASKS=true
FEATURE_CRM=true
FEATURE_INVOICING=true
# ... etc
```

## 🧪 Testing

### Backend Tests
```bash
make test
# or
docker compose exec php php artisan test
```

### Frontend Tests
```bash
docker compose run --rm node npm run test:unit
```

### E2E Tests
```bash
docker compose run --rm node npm run test:e2e
```

## 📊 Code Quality

### Backend
- **PHPStan** - Static analysis
- **Laravel Pint** - Code formatting
- **PHP Insights** - Code quality analysis

### Frontend
- **ESLint** - JavaScript/TypeScript linting
- **Prettier** - Code formatting
- **TypeScript** - Type checking

## 🚀 Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   cp api/env.example api/.env.production
   # Configure production settings
   ```

2. **Build Frontend**
   ```bash
   cd app
   npm run build
   ```

3. **Deploy Backend**
   ```bash
   cd api
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Docker Production
```bash
docker compose -f docker-compose.prod.yml up -d
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Use TypeScript for frontend code
- Follow Vue 3 Composition API patterns
- Document API endpoints

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [Wiki](https://github.com/alintentu/atlas/wiki)
- **Issues**: [GitHub Issues](https://github.com/alintentu/atlas/issues)
- **Discussions**: [GitHub Discussions](https://github.com/alintentu/atlas/discussions)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - The PHP framework
- [Vue.js](https://vuejs.org/) - The progressive JavaScript framework
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- [Docker](https://www.docker.com/) - Container platform

---

Built with ❤️ by the Atlas team
 
## Farmer App Trial Tasks (Backend Scaffold)

This repo now includes a backend scaffold aligned with the Farmer App trial tasks:

- Admin user management (invite, list with search, edit fields)
- Content Library for PDFs (upload, parse text, extract images, embeddings, activation toggles)

What changed:
- Switched DB to PostgreSQL + PGVector (Docker image `pgvector/pgvector:pg16`).
- New migrations for `users` extra fields and content tables.
- Embeddings driver added: mock (default) or OpenAI (set `OPENAI_API_KEY`).
- Admin APIs under `/api/admin/*` behind `auth:sanctum` and `role:owner|admin`.

Endpoints:
- `GET /api/admin/users` – `q` (name/email/region), `per_page`.
- `POST /api/admin/users` – invite: `name, email, role, region?, language?`.
- `PUT /api/admin/users/{id}` – edit: `name?, city?, phone?, region?, language?, profile_image?`.
- `GET /api/admin/content/pdfs` – list: `q, language, status`.
- `POST /api/admin/content/pdfs` – multipart upload: `name, description?, language?, is_active?, pdf`.
- `GET /api/admin/content/pdfs/{id}` – PDF with pages/images.
- `PATCH /api/admin/content/pdfs/{id}/pages/{pageId}/toggle` – toggle active.
- `PATCH /api/admin/content/pdfs/{id}/images/{imageId}/toggle` – toggle active.

Notes:
- Text extraction uses `smalot/pdfparser` if installed; otherwise a minimal fallback is used in dev.
- Image extraction uses Imagick if available; already included in the API Dockerfile.
- To enable OpenAI embeddings set in `api/.env`: `EMBEDDINGS_DRIVER=openai`, `OPENAI_API_KEY` and optionally `OPENAI_EMBEDDING_MODEL`.
