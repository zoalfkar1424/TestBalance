@echo off
echo ====================================
echo Balance Management System - Docker Setup
echo ====================================
echo.

echo Step 1: Starting Docker containers...
docker-compose up -d
if %errorlevel% neq 0 (
    echo ERROR: Failed to start containers. Make sure Docker Desktop is running.
    pause
    exit /b 1
)
echo Containers started successfully!
echo.

echo Step 2: Waiting for database to be ready...
set max_attempts=30
set attempt=0

:wait_db
set /a attempt+=1
if %attempt% gtr %max_attempts% (
    echo ERROR: Database failed to start after %max_attempts% attempts
    pause
    exit /b 1
)

docker-compose exec -T db pg_isready -U balance_user > nul 2>&1
if %errorlevel% neq 0 (
    echo Waiting for database... attempt %attempt%/%max_attempts%
    timeout /t 2 /nobreak > nul 2>&1
    goto wait_db
)
echo Database is ready!
echo.

echo Step 3: Installing Composer dependencies...
docker-compose exec app composer install --no-interaction
echo.

echo Step 4: Running database migrations...
docker-compose exec app php artisan migrate --force
echo.

echo Step 5: Clearing cache...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
echo.

echo ====================================
echo Setup Complete!
echo ====================================
echo.
echo Your application is now running at: http://localhost:8000
echo.
echo Next steps:
echo 1. Create test users: docker-compose exec app php artisan tinker
echo    Then run: User::factory()->create(['name' => 'User 1', 'email' => 'user1@test.com']);
echo.
echo 2. Run tests: docker-compose exec app php artisan test --filter=BalanceModuleTest
echo.
echo 3. Test the API endpoints:
echo    - POST http://localhost:8000/api/deposit
echo    - POST http://localhost:8000/api/withdraw
echo    - POST http://localhost:8000/api/transfer
echo    - GET http://localhost:8000/api/balance/{user_id}
echo.
pause
