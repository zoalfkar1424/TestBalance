

## About Environment
- Application Name ......................................................................................................................... Laravel
- Laravel Version .......................................................................................................................... 10.37.3
- PHP Version ............................................................................................................................... 8.1.10
- Composer Version ........................................................................................................................... 2.2.9
- Environment ................................................................................................................................ local
- Debug Mode ............................................................................................................................... ENABLED
- Mail ........................................................................................................................................ smtp
- Queue ....................................................................................................................................... sync
- Session ..................................................................................................................................... file

## Database server
- Server: 127.0.0.1 via TCP/IP
- Server type: MariaDB
- Server connection: SSL is not being used Documentation
- Server version: 10.4.25-MariaDB - mariadb.org binary distribution
- Protocol version: 10
- User: root@localhost
- Server charset: UTF-8 Unicode (utf8mb4)

## Run the Project
- Clone the project from GitHub.
- Install dependencies: Once the repository is cloned, navigate to the project's root directory and run the following command to install the project dependencies:
   * composer install
- Configure environment: Instruct them to copy the `.env.example` file in the project's root directory and rename it to `.env`. They should then open the `.env` file and set up the database connection details, including database name, username, and password. 
- Generate an application key: Run the following command to generate an application key for the Laravel project:
   * php artisan key:generate 
- Migrate the database: Instruct them to run the following command to run the database migrations and create the necessary tables:
   * php artisan migrate 
- Serve the application: To run the Laravel project locally, instruct them to run the following command:
   * php artisan serve 
- Access the application: Finally, instruct them to open their web browser and enter `http://localhost:8000` in the address bar. They should see the Laravel project running.
