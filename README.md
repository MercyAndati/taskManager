# Task Manager API
A laravel based REST API for manageing tasks with status progression and reporting capabilities.

## Requirements
- PHP 8.2+
- Composer
- MySql

## How to run locally
1. Clone the repository and navigate to the project directory: 'cd taskManager'
2. install dependencies: 'composer install'
3. copy the environment file andgenerate an app key: 'copy .env.example .env' (Windows) or 'cp .env.example .env' (Mac/Linux) 'php artisan key:generate'
4. Update the '.env' file with your local MySql credentials
5. Run ftabase migrations to create the tables: php artisan migrate
6. Start the local developmeny server: php artisan serve