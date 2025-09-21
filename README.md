# Laravel Application Architecture

This repository is a lightweight starter template that defines the base application architecture I use for new Laravel projects. It includes a recommended directory layout, example configuration, Docker support, and a minimal set of helpers to get started quickly.

## Quick checklist

- Requirements: PHP 8.1+, Composer, Docker & Docker Compose (optional but recommended)

## Installation (Docker + Makefile)

This template includes a `Makefile` with convenient targets to prepare the project. From the repository root use the following workflow:

```bash
# build docker images (optional: useful when you changed Dockerfile)
make images

# start services in background
docker compose up -d

# generate local self-signed TLS certs (optional)
make certs

# install project dependencies and prepare .env
make install

# run database migrations and seeders
docker compose exec app php artisan migrate --seed --force
```

Notes:
- `make install` runs `composer install`, copies `.env.example` to `.env`, and generates the app key inside the container (it already takes care of common PHP setup).
- If your environment uses the legacy `docker-compose` command, replace `docker compose` with `docker-compose` above.
- Use `make bash` to open a shell in the `app` container, and `make fix-permissions` if you need to fix storage/cache permissions.

## Project structure (high level)

- `app/` - Domain and HTTP layers organized by feature. Example: `Domains/Auth/Models/User.php`.
- `app/Domains/` - Domain layer that have all the business entities separated by it Domain (Bounded Context). Each domain has its own directories and files such as (Enums, Actions, Services, Models, Events, etc.)
- `docker/` - Dockerfile and service config for development.
- `.github/workflows` - CI/CD pipeline files using github actions.

This template intentionally uses a domain-oriented layout under `app/Domains` to encourage feature grouping and separation.

### 🏗️ **Current Architecture Overview**

```
app/
├── Domains/                    # Business domains (bounded contexts)
│   ├── User/
│   │   ├── Actions/           # Domain actions/use cases
│   │   ├── Models/            # Domain models
│   │   └── Services/          # Domain services
│   └── Management/
│       └── Enums/             # Domain enums
├── Http/                      # HTTP layer
│   └── Api/
│       └── V1/
│           └── Controllers/   # API controllers
│              ├── Actions/    # Single Action Controllers
│              └── Data/       # Data/Information Controllers
│                  └── Screens # Specific Screen Data Controllers
└── Support/                   # Shared utilities
    ├── Commands               # Development Commands
    ├── Http/Responses/        # Response utilities
    └── Traits/                # Shared traits
```

## Simple Domain Commands

These commands extend Laravel's built-in commands to work with your domain architecture. They are located in `app/Support/Commands/` to keep the main project structure clean.

## 🚀 **Commands Available**

### 1. Create API Request
```bash
# Basic usage
php artisan make:api-request User CreateUserRequest

# With different API version
php artisan make:api-request Product UpdateProductRequest --api-version=V2

# Force overwrite existing
php artisan make:api-request User LoginRequest --force

php artisan make:domain-model Product Product

php artisan make:domain-policy Product ProductPolicy

php artisan make:domain-service Product ProductService --bind
```

**Creates**: `app/Http/Api/V1/Requests/User/CreateUserRequest.php`

### 2. Create API Controller
```bash
# Basic controller
php artisan make:api-controller User UserController

# Resource controller (with CRUD methods)
php artisan make:api-controller User UserController --resource

# Invokable controller
php artisan make:api-controller Auth LoginController --invokable

# With model binding
php artisan make:api-controller User UserController --resource --model=User
```

**Creates**: `app/Http/Api/V1/Controllers/User/UserController.php`

### 3. Create API Resource
```bash
# Basic resource
php artisan make:api-resource User UserResource

# Resource collection
php artisan make:api-resource User UserResource --collection

# Different API version
php artisan make:api-resource Product ProductResource --api-version=V2
```

**Creates**: `app/Http/Api/V1/Resources/User/UserResource.php`

### 4. Create Domain Model
```bash
# Basic model
php artisan make:domain-model User User

# Model with migration
php artisan make:domain-model User User --migration

# Model with factory
php artisan make:domain-model User User --factory

# Model with everything (migration, factory, controller, resource)
php artisan make:domain-model User User --all

# Model with resource controller
php artisan make:domain-model Product Product --controller --resource
```

**Creates**: `app/Domains/User/Models/User.php`

## 🚀 **CI/CD Pipeline**

This template includes GitHub Actions workflows for automated testing and deployment. The CI/CD setup is designed to be simple and easily customizable.

### Available Workflows

#### 1. **Continuous Integration (`ci.yml`)**
- **Trigger**: Workflow call (reusable by other workflows)
- **Purpose**: Run automated tests with MySQL database
- **Features**:
  - PHP 8.3 setup with extensions
  - MySQL 8.0 service container
  - Composer dependency installation
  - Database migrations and testing
  - Basic Laravel test suite execution

#### 2. **Production Deployment (`deploy-production.yml`)**
- **Trigger**: Manual workflow dispatch (can be configured for `master` branch)
- **Purpose**: Deploy to production server after successful tests
- **Process**:
  1. Runs CI tests first
  2. Connects to production server via SSH
  3. Executes deployment commands:
     - Enable maintenance mode
     - Pull latest code from repository
     - Install/update Composer dependencies
     - Run database migrations
     - Clear and optimize caches
     - Disable maintenance mode

#### 3. **Development Deployment (`deploy-dev.yml`)**
- **Trigger**: Manual workflow dispatch (can be configured for `develop` branch)
- **Purpose**: Deploy to development/staging server
- **Process**: Similar to production but for development environment

### Configuration

To use these workflows:

1. **Set up repository secrets** in GitHub:
   ```
   VPS_HOST       # Your production server IP/domain
   VPS_USER       # SSH username
   VPS_SSH_KEY    # Private SSH key for server access
   VPS_PATH       # Path to your application on the server
   
   DEV_VPS_HOST             # Development server details (optional)
   DEV_VPS_USER             # Development SSH username
   DEV_VPS_SSH_KEY          # Development SSH private key
   DEV_VPS_PATH             # Development application path
   ```

2. **Customize triggers** by uncommenting the `push` events in workflow files
3. **Modify deployment commands** in the workflow files as needed for your server setup

### Manual Deployment

You can trigger deployments manually from GitHub Actions tab:
- Go to your repository → Actions tab
- Select "Deploy To Production" or "Deploy To Development"
- Click "Run workflow" button

## Troubleshooting

- Permission issues: If you see storage or bootstrap cache permission errors, ensure `storage/` and `bootstrap/cache` are writable by the webserver or adjust permissions using the included helper scripts in `docker/php/fix-permissions.sh`.