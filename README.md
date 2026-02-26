<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Simple Education API

A modern RESTful API backend built with Laravel 12. It provides administrative endpoints for managing Languages, Topics, and Courses, as well as public-facing endpoints for the course catalog. The API uses Laravel Passport for OAuth2 string authentication and Spatie Permission for Role-Based Access Control (RBAC).

## Stack

- **Framework**: Laravel 12
- **Authentication**: Laravel Passport
- **Role Management**: Spatie Laravel Permissions
- **Database**: PostgreSQL
- **Testing**: PHPUnit

---

## 🚀 Installation Guide

Follow these steps to install and run the project from a fresh clone.

### 1. Prerequisites
Ensure you have the following installed on your local machine:
- PHP 8.2 or higher
- Composer
- Git

### 2. Clone the Repository
Clone the project to your local environment and navigate into the directory:
```bash
git clone <repository-url>
cd simple-education
```

### 3. Install Dependencies
Run Composer to install all PHP packages required by the application:
```bash
composer install
```

### 4. Environment Setup
Copy the example environment file into a new `.env` file:
```bash
cp .env.example .env
```
Generate the application encryption key:
```bash
php artisan key:generate
```

Update your `.env` file with your PostgreSQL database credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 5. Database Setup & Migration
Ensure your PostgreSQL server is running and the database specified in `.env` has been created. 

Run the migrations to create the tables, and seed the database (this will seed the standard Spatie roles: `admin` and `user`):
```bash
php artisan migrate --seed
```

### 6. Install Passport
Laravel Passport handles API authentication. You must install Passport to generate the encryption keys and personal access clients:
```bash
php artisan passport:install
```

### 7. Running the Application
Start the local development server:
```bash
php artisan serve
```
Your API will now be accessible at `http://localhost:8000`.

---

## 🧪 Running Tests

The application is thoroughly tested using PHPUnit. Tests cover Authentication, Role validation, and all CRUD endpoints (Topics, Languages, Courses).

To run the complete test suite:
```bash
php artisan test
```

## 🔒 Endpoints Overview

- **Auth**: `/api/register`, `/api/login`, `/api/logout`
- **Public Core**: 
  - `GET /api/courses` (List all courses)
  - `GET /api/courses/{course}` (View course detail)
- **Admin Core**:
  - CRUD operations for `/api/topics`
  - CRUD operations for `/api/languages`
  - Create, Update, Delete operations for `/api/courses`
