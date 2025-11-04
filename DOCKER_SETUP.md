# Docker Setup Guide for Balance Management System

## Prerequisites
- Docker Desktop installed on Windows
- Docker Compose installed (comes with Docker Desktop)

## Setup Steps

### 1. Start Docker Containers
```bash
docker-compose up -d
```

This command will:
- Build the Laravel application container
- Start Nginx web server (accessible on port 8000)
- Start PostgreSQL database (accessible on port 5432)

### 2. Install Composer Dependencies (if needed)
```bash
docker-compose exec app composer install
```

### 3. Run Database Migrations
```bash
docker-compose exec app php artisan migrate
```

This will create the necessary tables:
- users
- balances
- transactions

### 4. Create Test Users
```bash
docker-compose exec app php artisan tinker
```

Then in the tinker console, run:
```php
User::factory()->create(['name' => 'Test User 1', 'email' => 'user1@test.com']);
User::factory()->create(['name' => 'Test User 2', 'email' => 'user2@test.com']);
exit
```

### 5. Run Tests
```bash
docker-compose exec app php artisan test --filter=BalanceModuleTest
```

## Testing the API

### Using curl:

**Deposit:**
```bash
curl -X POST http://localhost:8000/api/deposit -H "Content-Type: application/json" -d "{\"user_id\": 1, \"amount\": 500.00, \"comment\": \"Initial deposit\"}"
```

**Get Balance:**
```bash
curl http://localhost:8000/api/balance/1
```

**Withdraw:**
```bash
curl -X POST http://localhost:8000/api/withdraw -H "Content-Type: application/json" -d "{\"user_id\": 1, \"amount\": 200.00, \"comment\": \"Test withdrawal\"}"
```

**Transfer:**
```bash
curl -X POST http://localhost:8000/api/transfer -H "Content-Type: application/json" -d "{\"from_user_id\": 1, \"to_user_id\": 2, \"amount\": 100.00, \"comment\": \"Transfer to friend\"}"
```

## Useful Docker Commands

### View Logs
```bash
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f nginx
```

### Stop Containers
```bash
docker-compose down
```

### Stop and Remove All Data
```bash
docker-compose down -v
```

### Restart Containers
```bash
docker-compose restart
```

### Access Container Shell
```bash
docker-compose exec app bash
```

### Check Running Containers
```bash
docker-compose ps
```

### Clear Laravel Cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

## Troubleshooting

### Container won't start?
```bash
docker-compose down
docker-compose up -d --build
```

### Database connection issues?
Check if PostgreSQL is running:
```bash
docker-compose ps
```

### Permission issues?
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

### View database directly?
```bash
docker-compose exec db psql -U laravel -d laravel
```

Once in PostgreSQL:
```sql
\dt                           -- List all tables
SELECT * FROM users;          -- View users
SELECT * FROM balances;       -- View balances
SELECT * FROM transactions;   -- View transactions
\q                            -- Quit
```

## API Endpoints Summary

- POST `/api/deposit` - Deposit funds
- POST `/api/withdraw` - Withdraw funds
- POST `/api/transfer` - Transfer between users
- GET `/api/balance/{user_id}` - Get user balance

## Next Steps After Setup

1. Access the API at: `http://localhost:8000/api`
2. Use Postman, Insomnia, or curl to test endpoints
3. Run the test suite to verify everything works
4. Check the logs if you encounter any issues

