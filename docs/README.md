# E-Commerce Platform (Laravel + Vue.js)

A modern e-commerce platform built with Laravel backend (service layer pattern) and Vue.js frontend, featuring product catalog browsing, order checkout, and email notifications. Designed for 100% FREE deployment using AWS Free Tier and free email services.

## 🎯 Project Overview

This project implements a complete e-commerce system with a **single Laravel backend** organized using service layer pattern:

1. **CatalogService** - Handles product browsing and details
2. **CheckoutService** - Manages order placement with inventory validation
3. **InventoryService** - Tracks and manages product inventory
4. **EmailService** - Sends order confirmation emails with retry logic

### Key Features

- ✅ Product catalog browsing with pagination
- ✅ Product detail views with inventory availability
- ✅ Order placement with customer information
- ✅ Real-time inventory tracking with concurrency control
- ✅ Email notifications with retry mechanism (3 attempts over 10 minutes)
- ✅ Immutable orders (no modifications after placement)
- ✅ **100% FREE deployment** (AWS Free Tier + free email services)

## 🏗️ Architecture

### Monolithic Laravel Backend with Service Layer Pattern

```
┌────────────────────────────────────────────────────┐
│            Frontend (Vue.js)                        │
│            Port: 5173                               │
└─────────────────┬──────────────────────────────────┘
                  │ HTTP/REST API
                  ▼
┌─────────────────────────────────────────────────────┐
│       Laravel Backend (Single Application)          │
│       Port: 8000                                    │
│                                                     │
│  ┌─────────────────────────────────────────┐      │
│  │  Service Layer (Business Logic)         │      │
│  ├─────────────────────────────────────────┤      │
│  │  • CatalogService                       │      │
│  │  • CheckoutService                      │      │
│  │  • InventoryService                     │      │
│  │  • EmailService                         │      │
│  └─────────────────────────────────────────┘      │
│                                                     │
│  ┌─────────────────────────────────────────┐      │
│  │  Controllers (API Endpoints)            │      │
│  ├─────────────────────────────────────────┤      │
│  │  • ProductController                    │      │
│  │  • OrderController                      │      │
│  └─────────────────────────────────────────┘      │
│                                                     │
│  ┌─────────────────────────────────────────┐      │
│  │  Models (Database ORM)                  │      │
│  ├─────────────────────────────────────────┤      │
│  │  • Product                              │      │
│  │  • Order                                │      │
│  │  • OrderItem                            │      │
│  │  • EmailLog                             │      │
│  └─────────────────────────────────────────┘      │
└───────────────────┬─────────────────────────────────┘
                    │
                    ▼
          ┌──────────────────┐         ┌─────────────┐
          │  MySQL Database  │         │  MailHog    │
          │  Port: 3306      │         │  SMTP: 1025 │
          │                  │         │  UI: 8025   │
          │  • products      │         └─────────────┘
          │  • orders        │
          │  • order_items   │
          │  • email_logs    │
          └──────────────────┘
```

### Technology Stack

**Backend (Laravel)**:
- PHP 8.2+ with Laravel 12.x
- MySQL 8.0+ (single database with multiple tables)
- Service Layer Pattern for business logic organization
- Laravel Queue for async email jobs
- Nginx (reverse proxy)

**Frontend**:
- Vue.js 3.x with Composition API
- Vite (build tool & dev server)
- Axios (HTTP client)

**Infrastructure**:
- Docker & Docker Compose (local development)
- AWS EC2 t2.micro (FREE tier - 750 hours/month)
- AWS RDS db.t2.micro (FREE tier - 750 hours/month)
- AWS CloudFormation (Infrastructure-as-code)

**Email (100% FREE)**:
- MailHog (local development - unlimited)
- Mailtrap.io (testing - 500 emails/month free)
- Gmail SMTP (production - 500 emails/day free)

## 📋 Prerequisites

### Local Development

- **Docker Desktop** (or Docker Engine + Docker Compose)
  - [Download Docker Desktop](https://www.docker.com/products/docker-desktop/)
  - Minimum: 4GB RAM allocated to Docker

- **PHP 8.2+** (for local development without Docker)
  - [Download PHP](https://www.php.net/downloads)

- **Composer** (PHP package manager)
  - [Download Composer](https://getcomposer.org/download/)

- **Node.js 20+** (for frontend development)
  - [Download Node.js](https://nodejs.org/)

- **Git**
  - [Download Git](https://git-scm.com/downloads)

### AWS Deployment (Optional)

- **AWS Account** with Free Tier (12 months)
  - [Create AWS Account](https://aws.amazon.com/free/)
  - ⚠️ **IMPORTANT**: Set up billing alarm for $0.01 to avoid charges

- **AWS CLI** (for deployments)
  - [Install AWS CLI](https://aws.amazon.com/cli/)

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/ecommerce-platform.git
cd ecommerce-platform
```

### 2. Start Services with Docker Compose

```bash
cd infrastructure
docker-compose up -d
```

This will start all services:
- **Backend API**: http://localhost:8000
- **Frontend**: http://localhost:5173
- **MailHog UI**: http://localhost:8025 (view sent emails)
- **MySQL**: localhost:3306

### 3. Run Database Migrations

```bash
# Run migrations and seed sample data
docker-compose exec backend php artisan migrate --seed
```

### 4. Access the Application

Open your browser and navigate to:
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api/v1/products
- **MailHog** (view sent emails): http://localhost:8025

## 📁 Project Structure

```
ecommerce-platform/
├── backend/                  # Laravel backend application
│   ├── app/
│   │   ├── Models/          # Product, Order, OrderItem, EmailLog
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/  # ProductController, OrderController
│   │   │   └── Requests/    # Form request validation
│   │   ├── Services/        # Business logic layer
│   │   │   ├── CatalogService.php
│   │   │   ├── CheckoutService.php
│   │   │   ├── InventoryService.php
│   │   │   └── EmailService.php
│   │   └── Jobs/            # Queue jobs
│   │       └── SendOrderConfirmationEmail.php
│   ├── database/
│   │   ├── migrations/      # Database schema
│   │   └── seeders/         # Sample data
│   ├── routes/
│   │   └── api.php         # API routes
│   └── tests/              # PHPUnit tests
│
├── frontend/                # Vue.js frontend
│   ├── src/
│   │   ├── components/
│   │   │   ├── catalog/    # Product list, details
│   │   │   └── checkout/   # Checkout form, order confirmation
│   │   ├── pages/          # Page components
│   │   ├── services/       # API clients
│   │   └── router/         # Vue Router
│   └── tests/              # Vitest tests
│
├── infrastructure/
│   ├── docker-compose.yml  # Local development
│   ├── nginx/              # Nginx config
│   └── cloudformation/     # AWS infrastructure
│
├── tests/                  # Integration tests
└── docs/                   # Documentation
```

## 🔧 Development

### Running Services Individually

**Backend**:
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```

**Frontend**:
```bash
cd frontend
npm install
npm run dev
```

**Queue Worker** (for email jobs):
```bash
cd backend
php artisan queue:work
```

### Running Tests

**Backend (PHPUnit)**:
```bash
docker-compose exec backend php artisan test
```

**Frontend (Vitest)**:
```bash
cd frontend
npm run test:unit
```

## 🌐 API Documentation

### Product Endpoints

**GET** `/api/v1/products` - List all products with pagination
**GET** `/api/v1/products/{id}` - Get product details

### Order Endpoints

**POST** `/api/v1/orders` - Create new order
**GET** `/api/v1/orders/{orderNumber}` - Get order by order number
**GET** `/api/v1/orders/{id}` - Get order by ID

See [API.md](./API.md) for detailed API documentation.

## 💰 100% FREE Deployment Guide

This project is designed to run with ZERO costs using AWS Free Tier and free email services.

### Free Tier Resources Used

**AWS Free Tier (12 months)**:
- EC2 t2.micro instances: 750 hours/month
- RDS db.t2.micro: 750 hours/month, 20GB storage
- 15 GB/month data transfer out

**Email Services (Forever FREE)**:
- MailHog: Unlimited (local development)
- Mailtrap: 500 emails/month (testing)
- Gmail SMTP: 500 emails/day (production)

### Cost Monitoring

⚠️ **CRITICAL**: Set up billing alarms BEFORE deploying to AWS!

1. Go to AWS Billing Console
2. Create billing alarm for **$0.01**
3. You'll receive email if ANY charge occurs

See [FREE-TIER.md](../specs/1-ecommerce-microservices/FREE-TIER.md) for complete free setup guide.

## 📦 Deployment

### AWS Deployment with CloudFormation

```bash
# Deploy infrastructure
cd infrastructure/cloudformation
aws cloudformation create-stack \
  --stack-name ecommerce-platform \
  --template-body file://main.yaml \
  --capabilities CAPABILITY_IAM

# Wait for stack creation
aws cloudformation wait stack-create-complete \
  --stack-name ecommerce-platform

# Get outputs (endpoints, database URLs)
aws cloudformation describe-stacks \
  --stack-name ecommerce-platform \
  --query 'Stacks[0].Outputs'
```

See [DEPLOYMENT.md](./DEPLOYMENT.md) for detailed deployment instructions.

## 🧪 Testing the Platform

### End-to-End Test Flow

1. **Browse Products**: Visit http://localhost:5173
2. **View Product Details**: Click on any product
3. **Place Order**:
   - Add products to cart
   - Fill out checkout form
   - Submit order
4. **Check Email**: Visit http://localhost:8025 to see order confirmation email
5. **Verify Inventory**: Check that product quantities decreased

### Testing Email Retry Logic

1. Stop MailHog: `docker-compose stop mailhog`
2. Place an order (email will fail)
3. Check logs: `docker-compose logs backend`
4. Restart MailHog: `docker-compose start mailhog`
5. Email will retry and eventually succeed

## 🔍 Monitoring & Debugging

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
```

### Access Database

```bash
docker-compose exec db mysql -u ecommerce_user -pecommerce_password ecommerce_db
```

### View Email Queue

```bash
docker-compose exec backend php artisan queue:work --once
docker-compose exec backend php artisan queue:failed
docker-compose exec backend php artisan queue:retry all
```

## 📚 Additional Documentation

- [SETUP.md](./SETUP.md) - Detailed setup instructions
- [DEPLOYMENT.md](./DEPLOYMENT.md) - AWS deployment guide
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System architecture overview
- [API.md](./API.md) - Complete API reference
- [FREE-TIER.md](../specs/1-ecommerce-microservices/FREE-TIER.md) - Zero-cost setup guide

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🎓 Educational Purpose

This project was created as an educational/exam activity to demonstrate:
- Laravel service layer pattern
- API-first development
- Cloud infrastructure provisioning
- Docker containerization
- Cost-effective deployment strategies

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js Team
- Docker Team
- AWS Free Tier Program
- MailHog Project
- Mailtrap Service

## 📧 Support

For issues and questions:
- Create an issue in this repository
- Check existing documentation in `/docs`
- Review the FREE-TIER.md guide for cost-related questions

---

**Built with ❤️ using 100% FREE services**
