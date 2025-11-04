# User Balance Management API

Laravel-based application for managing user balances with deposit, withdrawal, and transfer operations.

## 📋 Requirements

- Docker & Docker Compose
- PHP 8.2+
- PostgreSQL 15
- Composer

## 🏗️ Project Structure

This project uses a **modular architecture** where each feature is organized in its own module:

```
app/Modules/
└── Balance/          # ✨ Balance management module
    ├── Actions/      # Business logic layer
    ├── Controllers/  # HTTP request handlers
    ├── DTO/          # Data Transfer Objects
    ├── Models/       # Eloquent models
    ├── Requests/     # Request validation
    └── Routes/       # Module-specific routes
```

## 🚀 Quick Start

### 1. Clone and Setup

```bash
git clone https://github.com/zoalfkar1424/TestBalance.git
cd TestBalance
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Start Docker Containers

```bash
docker-compose up -d
```

This will start:
- **Laravel App** (PHP 8.2-FPM)
- **Nginx** (Port 8000)
- **PostgreSQL** (Port 5432)

### 4. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 5. Access the API

The API is now available at: `http://localhost:8000/api`

## 📊 Database Schema

### Tables Created

**users**
- `id` - Primary key
- `name` - String
- `email` - String (unique)
- `password` - String
- `timestamps`

**balances**
- `id` - Primary key
- `user_id` - Foreign key to users (unique)
- `balance` - Decimal(15,2), default 0
- `timestamps`

**transactions**
- `id` - Primary key
- `user_id` - Foreign key to users
- `type` - Enum: deposit, withdraw, transfer_in, transfer_out
- `amount` - Decimal(15,2)
- `comment` - String (nullable)
- `related_transaction_id` - For linking transfer transactions
- `timestamps`

## 🔌 API Endpoints

### 1. Deposit Funds
```http
POST /api/deposit
Content-Type: application/json

{
  "user_id": 1,
  "amount": 500.00,
  "comment": "Пополнение через карту"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 500.00,
    "deposited_amount": 500.00
  },
  "message": "Deposit successful",
  "code": 200
}
```

### 2. Withdraw Funds
```http
POST /api/withdraw
Content-Type: application/json

{
  "user_id": 1,
  "amount": 200.00,
  "comment": "Покупка подписки"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 300.00,
    "withdrawn_amount": 200.00
  },
  "message": "Withdrawal successful",
  "code": 200
}
```

**Response (409 - Insufficient Balance):**
```json
{
  "success": false,
  "data": {
    "current_balance": 100.00,
    "requested_amount": 200.00
  },
  "message": "Insufficient balance",
  "code": 409
}
```

### 3. Transfer Between Users
```http
POST /api/transfer
Content-Type: application/json

{
  "from_user_id": 1,
  "to_user_id": 2,
  "amount": 150.00,
  "comment": "Перевод другу"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "from_user_id": 1,
    "to_user_id": 2,
    "amount": 150.00,
    "sender_new_balance": 150.00,
    "receiver_new_balance": 150.00
  },
  "message": "Transfer successful",
  "code": 200
}
```

### 4. Get User Balance
```http
GET /api/balance/{user_id}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "balance": 350.00
  },
  "message": "Balance retrieved successfully",
  "code": 200
}
```

**Response (404):**
```json
{
  "success": false,
  "data": null,
  "message": "User has no balance record",
  "code": 404
}
```

## ✅ Features Implemented

- ✅ **PHP 8.2** - Modern PHP version
- ✅ **PostgreSQL** - Database storage
- ✅ **Docker** - Fully containerized application
- ✅ **Database Transactions** - All operations are atomic
- ✅ **Balance Protection** - Prevents negative balances
- ✅ **Auto-creation** - Balance records created on first deposit
- ✅ **Transaction Logging** - Complete audit trail
- ✅ **Linked Transfers** - Transfer operations create linked records
- ✅ **Request Validation** - Comprehensive input validation
- ✅ **Proper HTTP Codes** - 200, 404, 409, 422, 500
- ✅ **Unit Tests** - Full test coverage

## 🧪 Running Tests

```bash
docker-compose exec app php artisan test --filter=BalanceModuleTest
```

### Test Coverage

The test suite includes:
- ✅ Deposit funds to user account
- ✅ Withdraw funds from user account
- ✅ Prevent withdrawal with insufficient balance
- ✅ Transfer funds between users
- ✅ Prevent transfer with insufficient balance
- ✅ Get user balance
- ✅ Return 404 for non-existent balance
- ✅ Validate deposit requests
- ✅ Prevent transfer to same user
- ✅ Verify linked transactions for transfers

## 📝 HTTP Status Codes

- **200** - Success
- **404** - User not found or no balance record
- **409** - Conflict (e.g., insufficient balance)
- **422** - Validation error (invalid input)
- **500** - Server error

## 🔧 Development

### Creating Test Users

```bash
docker-compose exec app php artisan tinker
```

```php
User::factory()->create(['name' => 'Test User 1', 'email' => 'user1@test.com']);
User::factory()->create(['name' => 'Test User 2', 'email' => 'user2@test.com']);
```

### Useful Commands

```bash
# View logs
docker-compose logs -f app

# Access container shell
docker-compose exec app bash

# Clear cache
docker-compose exec app php artisan cache:clear

# Run all tests
docker-compose exec app php artisan test

# Stop containers
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

## 🏛️ Architecture Highlights

### Modular Design
The Balance module is self-contained with:
- Models
- Controllers
- Actions (business logic)
- DTOs (data transfer)
- Requests (validation)
- Routes

### Transaction Safety
All balance operations use database transactions:
```php
DB::beginTransaction();
try {
    // Perform operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Handle error
}
```

### Action Pattern
Business logic is separated into Action classes:
- `DepositAction::execute()`
- `WithdrawAction::execute()`
- `TransferAction::execute()`
- `GetBalanceAction::execute()`

## 📚 Additional Documentation

See `BALANCE_MODULE_README.md` for detailed Balance module documentation.

## 🐳 Docker Services

- **app** - Laravel application (PHP 8.2-FPM)
- **nginx** - Web server (Port 8000)
- **db** - PostgreSQL 15 (Port 5432)

## 🔐 Environment Variables

Key variables in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=laravel_db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

## 📄 License

This is a test project for demonstration purposes.

---

**Created using modular Laravel architecture with clean separation of concerns.**
