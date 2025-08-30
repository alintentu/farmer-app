#!/bin/bash

set -e

echo "🚀 Atlas Microservices Migration Script"
echo "======================================"

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

# Check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed"
        exit 1
    fi
    
    # Check kubectl
    if ! command -v kubectl &> /dev/null; then
        print_error "kubectl is not installed"
        exit 1
    fi
    
    # Check helm
    if ! command -v helm &> /dev/null; then
        print_error "Helm is not installed"
        exit 1
    fi
    
    print_success "All prerequisites are met"
}

# Setup Kubernetes cluster
setup_kubernetes() {
    print_status "Setting up Kubernetes cluster..."
    
    # Create namespace
    kubectl create namespace atlas --dry-run=client -o yaml | kubectl apply -f -
    
    # Add Helm repositories
    helm repo add kong https://charts.konghq.com
    helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
    helm repo add elastic https://helm.elastic.co
    helm repo update
    
    print_success "Kubernetes setup completed"
}

# Deploy API Gateway
deploy_api_gateway() {
    print_status "Deploying API Gateway..."
    
    # Deploy Kong
    helm install kong kong/kong \
        --namespace atlas \
        --set ingressController.installCRDs=false \
        --set proxy.type=LoadBalancer
    
    # Wait for Kong to be ready
    kubectl wait --for=condition=ready pod -l app.kubernetes.io/name=kong -n atlas --timeout=300s
    
    print_success "API Gateway deployed"
}

# Deploy monitoring stack
deploy_monitoring() {
    print_status "Deploying monitoring stack..."
    
    # Deploy Prometheus
    helm install prometheus prometheus-community/kube-prometheus-stack \
        --namespace atlas \
        --set grafana.enabled=true \
        --set prometheus.prometheusSpec.serviceMonitorSelectorNilUsesHelmValues=false
    
    # Deploy Elasticsearch
    helm install elasticsearch elastic/elasticsearch \
        --namespace atlas \
        --set replicas=1 \
        --set minimumMasterNodes=1
    
    # Deploy Kibana
    helm install kibana elastic/kibana \
        --namespace atlas \
        --set elasticsearchHosts=http://elasticsearch-master:9200
    
    print_success "Monitoring stack deployed"
}

# Create service repositories
create_service_repos() {
    print_status "Creating service repositories..."
    
    services=("atlas-core" "atlas-tasks" "atlas-crm" "atlas-invoicing" "atlas-marketing" "atlas-analytics" "atlas-communication" "atlas-files" "atlas-search")
    
    for service in "${services[@]}"; do
        print_status "Creating repository for $service..."
        
        # Create directory structure
        mkdir -p "../$service"
        
        # Initialize git repository
        cd "../$service"
        git init
        
        # Create basic structure
        mkdir -p {src,tests,docker,k8s,docs}
        
        # Create README
        cat > README.md << EOF
# $service

Microservice for Atlas SaaS Platform.

## Features
- Service-specific functionality
- Subscription-based access control
- Health checks
- Monitoring integration

## Development
\`\`\`bash
docker build -t $service .
docker run -p 8000:8000 $service
\`\`\`

## Deployment
\`\`\`bash
kubectl apply -f k8s/
\`\`\`
EOF
        
        # Create Dockerfile
        cat > Dockerfile << EOF
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache \\
    git \\
    curl \\
    libpng-dev \\
    libxml2-dev \\
    zip \\
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
EOF
        
        # Create Kubernetes deployment
        cat > k8s/deployment.yaml << EOF
apiVersion: apps/v1
kind: Deployment
metadata:
  name: $service
  namespace: atlas
spec:
  replicas: 2
  selector:
    matchLabels:
      app: $service
  template:
    metadata:
      labels:
        app: $service
    spec:
      containers:
        - name: $service
          image: $service:latest
          ports:
            - containerPort: 8000
          env:
            - name: APP_ENV
              value: "production"
            - name: APP_DEBUG
              value: "false"
          livenessProbe:
            httpGet:
              path: /health
              port: 8000
            initialDelaySeconds: 30
            periodSeconds: 10
          readinessProbe:
            httpGet:
              path: /health
              port: 8000
            initialDelaySeconds: 5
            periodSeconds: 5
          resources:
            requests:
              memory: "128Mi"
              cpu: "100m"
            limits:
              memory: "512Mi"
              cpu: "500m"
---
apiVersion: v1
kind: Service
metadata:
  name: $service
  namespace: atlas
spec:
  selector:
    app: $service
  ports:
    - protocol: TCP
      port: 8000
      targetPort: 8000
  type: ClusterIP
EOF
        
        # Initialize git
        git add .
        git commit -m "Initial commit: $service microservice"
        
        cd - > /dev/null
        
        print_success "Repository created for $service"
    done
}

# Migrate database schemas
migrate_databases() {
    print_status "Migrating database schemas..."
    
    # Create database migration scripts for each service
    services=("tasks" "crm" "invoicing" "marketing" "analytics" "communication" "files" "search")
    
    for service in "${services[@]}"; do
        print_status "Creating database schema for $service..."
        
        cat > "database/migrations/create_${service}_schema.sql" << EOF
-- $service Service Database Schema
-- This will be executed when the service is deployed

-- Create service-specific tables
-- Add your service-specific schema here

-- Example for tasks service:
-- CREATE TABLE IF NOT EXISTS projects (
--     id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
--     tenant_id UUID NOT NULL,
--     name VARCHAR(255) NOT NULL,
--     description TEXT,
--     status VARCHAR(50) DEFAULT 'active',
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- Example for crm service:
-- CREATE TABLE IF NOT EXISTS contacts (
--     id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
--     tenant_id UUID NOT NULL,
--     first_name VARCHAR(100) NOT NULL,
--     last_name VARCHAR(100) NOT NULL,
--     email VARCHAR(255) UNIQUE,
--     phone VARCHAR(50),
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );
EOF
        
        print_success "Database schema created for $service"
    done
}

# Setup CI/CD pipelines
setup_cicd() {
    print_status "Setting up CI/CD pipelines..."
    
    # Create GitHub Actions workflow
    cat > .github/workflows/microservices-ci.yml << EOF
name: Microservices CI/CD

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        service: [core, tasks, crm, invoicing, marketing, analytics, communication, files, search]
    
    steps:
    - uses: actions/checkout@v4
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-interaction
    
    - name: Run tests
      run: composer test
    
    - name: Build Docker image
      run: docker build -t atlas-\${{ matrix.service }} .
    
    - name: Push to registry
      run: |
        echo \${{ secrets.DOCKER_PASSWORD }} | docker login -u \${{ secrets.DOCKER_USERNAME }} --password-stdin
        docker push atlas-\${{ matrix.service }}:latest

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - name: Deploy to Kubernetes
      run: |
        kubectl apply -f k8s/
        kubectl rollout status deployment/atlas-core -n atlas
EOF
    
    print_success "CI/CD pipelines configured"
}

# Create service mesh configuration
setup_service_mesh() {
    print_status "Setting up service mesh..."
    
    # Install Istio
    curl -L https://istio.io/downloadIstio | sh -
    cd istio-*
    export PATH=\$PWD/bin:\$PATH
    
    # Install Istio with default profile
    istioctl install --set profile=demo -y
    
    # Enable automatic sidecar injection for atlas namespace
    kubectl label namespace atlas istio-injection=enabled
    
    # Create Istio VirtualService for API Gateway
    cat > k8s/istio-gateway.yaml << EOF
apiVersion: networking.istio.io/v1beta1
kind: Gateway
metadata:
  name: atlas-gateway
  namespace: atlas
spec:
  selector:
    istio: ingressgateway
  servers:
  - port:
      number: 80
      name: http
      protocol: HTTP
    hosts:
    - "*"
---
apiVersion: networking.istio.io/v1beta1
kind: VirtualService
metadata:
  name: atlas-vs
  namespace: atlas
spec:
  hosts:
  - "*"
  gateways:
  - atlas-gateway
  http:
  - match:
    - uri:
        prefix: /api/auth
    route:
    - destination:
        host: atlas-core
        port:
          number: 8000
  - match:
    - uri:
        prefix: /api/tasks
    route:
    - destination:
        host: atlas-tasks
        port:
          number: 8000
  - match:
    - uri:
        prefix: /api/crm
    route:
    - destination:
        host: atlas-crm
        port:
          number: 8000
EOF
    
    print_success "Service mesh configured"
}

# Main execution
main() {
    print_status "Starting Atlas microservices migration..."
    
    check_prerequisites
    setup_kubernetes
    deploy_api_gateway
    deploy_monitoring
    create_service_repos
    migrate_databases
    setup_cicd
    setup_service_mesh
    
    print_success "🎉 Atlas microservices migration completed!"
    echo ""
    echo "Next steps:"
    echo "  1. Review and customize service configurations"
    echo "  2. Implement service-specific business logic"
    echo "  3. Add subscription middleware to each service"
    echo "  4. Test service communication"
    echo "  5. Deploy to production"
    echo ""
    echo "Services created:"
    echo "  - atlas-core (Authentication & Authorization)"
    echo "  - atlas-tasks (Project & Task Management)"
    echo "  - atlas-crm (Customer Relationship Management)"
    echo "  - atlas-invoicing (Billing & Financial)"
    echo "  - atlas-marketing (Marketing Automation)"
    echo "  - atlas-analytics (Data Analytics)"
    echo "  - atlas-communication (Notifications)"
    echo "  - atlas-files (File Storage)"
    echo "  - atlas-search (Global Search)"
    echo ""
    echo "Infrastructure deployed:"
    echo "  - Kubernetes cluster with namespace 'atlas'"
    echo "  - Kong API Gateway"
    echo "  - Prometheus + Grafana monitoring"
    echo "  - Elasticsearch + Kibana logging"
    echo "  - Istio service mesh"
}

# Run main function
main "$@"
