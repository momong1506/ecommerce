# E-Commerce Platform - Setup Guide

This guide will help you set up the E-Commerce Platform development environment locally using Docker.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **Docker** (version 20.10 or higher)
- **Docker Compose** (version 2.0 or higher)
- **Git**

## Quick Start

### 1. Clone the Repository

```bash
git clone <repository-url>
cd raymond-assignment
```

### 2. Backend Setup

#### Configure Environment Variables

```bash
cd backend
cp .env.example .env
```

The `.env` file is already configured with Docker-specific settings:
- Database host: `db`
- Database name: `ecommerce_db`
- Mail host: `mailhog` (for development)

#### Install Dependencies

```bash
composer install
```

#### Generate Application Key

```bash
php artisan key:generate
```

### 3. Frontend Setup

#### Configure Environment Variables

```bash
cd ../frontend
cp .env.example .env
```

The `.env` file contains:
- API base URL: `http://localhost:8000/api/v1`

#### Install Dependencies

```bash
npm install
```

### 4. Start the Application with Docker

From the project root directory:

```bash
cd infrastructure
docker-compose up -d
```

This will start the following services:
- **Backend** (Laravel): http://localhost:8000
- **Frontend** (Vue.js): http://localhost:5173
- **Database** (MySQL): localhost:3306
- **MailHog** (Email testing): http://localhost:8025

### 5. Initialize the Database

Run migrations and seed the database:

```bash
docker-compose exec backend php artisan migrate --seed
```

This will:
- Create all necessary database tables (products, orders, order_items, email_logs)
- Seed the database with 10 sample products

### 6. Access the Application

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api/v1
- **MailHog** (Email testing): http://localhost:8025
- **Health Check**: http://localhost:8000/up

## Development Workflow

### Running the Backend

With Docker:
```bash
docker-compose exec backend php artisan serve --host=0.0.0.0 --port=9000
```

Without Docker:
```bash
cd backend
php artisan serve
```

### Running the Frontend

With Docker:
```bash
docker-compose exec frontend npm run dev
```

Without Docker:
```bash
cd frontend
npm run dev
```

### Running Migrations

```bash
docker-compose exec backend php artisan migrate
```

### Running Seeders

```bash
docker-compose exec backend php artisan db:seed
```

### Fresh Migration (Reset Database)

```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

### Queue Workers (Email Processing)

The email service uses Laravel's queue system for retry logic:

```bash
docker-compose exec backend php artisan queue:work
```

### Viewing Logs

```bash
# Backend logs
docker-compose logs -f backend

# Frontend logs
docker-compose logs -f frontend

# All logs
docker-compose logs -f
```

## Testing Emails

This project uses **MailHog** for local email testing (100% FREE):

1. Access MailHog at http://localhost:8025
2. All emails sent by the application will appear in the MailHog inbox
3. No configuration needed - it works out of the box!

## Troubleshooting

### Port Already in Use

If you get a port conflict error, check which ports are in use:

```bash
# Check port 8000 (backend)
lsof -i :8000

# Check port 5173 (frontend)
lsof -i :5173

# Check port 3306 (database)
lsof -i :3306
```

Stop the conflicting service or change the port in `docker-compose.yml`.

### Database Connection Issues

1. Ensure the database container is running:
   ```bash
   docker-compose ps
   ```

2. Check database logs:
   ```bash
   docker-compose logs db
   ```

3. Verify environment variables in `backend/.env`:
   ```
   DB_HOST=db
   DB_DATABASE=ecommerce_db
   DB_USERNAME=ecommerce_user
   DB_PASSWORD=ecommerce_password
   ```

### Frontend Can't Connect to Backend

1. Verify CORS settings in `backend/config/cors.php`
2. Check that the backend is running on port 8000
3. Verify `VITE_API_BASE_URL` in `frontend/.env`

### Composer/NPM Install Fails

```bash
# Clear composer cache
docker-compose exec backend composer clear-cache

# Clear npm cache
docker-compose exec frontend npm cache clean --force
```

## API Endpoints

### Catalog Service

- `GET /api/v1/catalog/products` - List all products
- `GET /api/v1/catalog/products/{id}` - Get single product

### Checkout Service

- `POST /api/v1/checkout/orders` - Create new order
- `GET /api/v1/checkout/orders/{orderNumber}` - Get order details

### Email Service

- `GET /api/v1/email/logs` - Get email logs
- `POST /api/v1/email/logs/{id}/retry` - Retry failed email

## Architecture Overview

This is a **single Laravel backend** application with a **service layer pattern**:

```
backend/
├── app/
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic layer
│   │   ├── CatalogService.php
│   │   ├── CheckoutService.php
│   │   ├── InventoryService.php
│   │   └── EmailService.php
│   └── Http/
│       └── Controllers/     # API controllers
└── database/
    ├── migrations/          # Database schema
    └── seeders/            # Sample data

frontend/
├── src/
│   ├── pages/              # Vue.js pages
│   ├── components/         # Reusable components
│   ├── services/           # API client services
│   └── router/             # Vue Router configuration
```

## Database Schema

- **products**: Product catalog with inventory
- **orders**: Customer orders
- **order_items**: Order line items (many-to-many)
- **email_logs**: Email delivery tracking with retry logic

## Next Steps

After setup is complete, proceed with:

1. **Phase 3**: Implement product catalog endpoints and UI
2. **Phase 4**: Implement checkout process
3. **Phase 5**: Implement email notification system
4. **Phase 6**: Add comprehensive testing
5. **Phase 7**: Deploy to AWS Free Tier

## Getting Help

- Check the main README.md for architecture details
- Review the API specifications in `specs/1-ecommerce-microservices/`
- Check CLAUDE.md for coding guidelines and conventions

## Clean Up

To stop and remove all containers:

```bash
docker-compose down
```

To remove volumes (this will delete the database):

```bash
docker-compose down -v
```
