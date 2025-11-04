# Balance Management Module

This module implements a user balance management system with deposit, withdrawal, and transfer capabilities.

## Structure

Following the same modular architecture as Article and Comment modules:

```
app/Modules/Balance/
├── Actions/              # Business logic
│   ├── DepositAction.php
│   ├── WithdrawAction.php
│   ├── TransferAction.php
│   └── GetBalanceAction.php
├── Controllers/          # HTTP request handlers
│   └── BalanceController.php
├── DTO/                  # Data Transfer Objects
│   ├── DepositDTO.php
│   ├── WithdrawDTO.php
│   └── TransferDTO.php
├── Models/               # Eloquent models
│   ├── Balance.php
│   └── Transaction.php
├── Requests/             # Form validation
│   ├── DepositRequest.php
│   ├── WithdrawRequest.php
│   └── TransferRequest.php
└── Routes/
    └── api.php           # Module routes
```

## Database Schema

### Balances Table
- `id` - Primary key
- `user_id` - Foreign key to users table (unique)
- `balance` - Decimal(15,2), default 0
- `timestamps`

### Transactions Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `type` - Enum: 'deposit', 'withdraw', 'transfer_in', 'transfer_out'
- `amount` - Decimal(15,2)
- `comment` - String (nullable)
- `related_transaction_id` - Foreign key to transactions table (for linking transfers)
- `timestamps`

## API Endpoints

### 1. Deposit Funds
**POST** `/api/deposit`

```json
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
**POST** `/api/withdraw`

```json
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
**POST** `/api/transfer`

```json
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
**GET** `/api/balance/{user_id}`

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

## Features

✅ **Database Transactions** - All operations use DB transactions for data integrity
✅ **Balance Protection** - Balance cannot go negative
✅ **Auto-creation** - Balance records are automatically created on first deposit
✅ **Transaction Logging** - All operations are logged in transactions table
✅ **Linked Transfers** - Transfer operations create linked transaction records
✅ **Validation** - Request validation using FormRequest classes
✅ **Error Handling** - Proper HTTP status codes (200, 404, 409, 422, 500)

## Transaction Types

- `deposit` - Funds added to account
- `withdraw` - Funds removed from account
- `transfer_in` - Funds received from another user
- `transfer_out` - Funds sent to another user

## Setup

1. Run migrations:
```bash
php artisan migrate
```

2. The routes are automatically registered in `RouteServiceProvider.php`

3. Make sure you have users in the database (from the users table)

## Business Logic

All business logic is contained in Action classes:
- `DepositAction::execute()` - Handles deposits
- `WithdrawAction::execute()` - Handles withdrawals with balance checks
- `TransferAction::execute()` - Handles transfers with balance validation
- `GetBalanceAction::execute()` - Retrieves user balance

## Error Codes

- **200** - Success
- **404** - User not found or no balance record
- **409** - Conflict (insufficient balance)
- **422** - Validation error
- **500** - Server error

