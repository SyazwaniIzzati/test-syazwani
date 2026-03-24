# ✅ Task Manager App

A simple To-Do List web application built with **Laravel** + **Vue.js (Inertia.js)** using Laravel Breeze as the starter kit.

---

## 🛠️ Tech Stack

- Laravel 10+
- Vue.js 3 (via Inertia.js)
- Laravel Breeze (Vue starter kit)
- Tailwind CSS
- SQLite

---

## ⚙️ Requirements

Make sure you have the following installed:

- PHP >= 8.1
- Composer
- Node.js >= 18.x & NPM
- Git

---

## 🚀 How to Set Up and Run Locally

### 1. Clone the Repository
```bash
git clone https://github.com/SyazwaniIzzati/test-syazwani.git
cd test-syazwani
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Run Database Migrations
```bash
php artisan migrate
```

> SQLite database is configured automatically. No extra database setup needed.

### 6. Build Frontend Assets
```bash
npm run dev
```

### 7. Start the Laravel Server

Open a second terminal and run:
```bash
php artisan serve
```

### 8. Open in Browser
```
http://127.0.0.1:8000
```

---

## 🗄️ Database Schema

### Tasks Table
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->boolean('is_completed')->default(false);
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamp('scheduled_time')->nullable();
    $table->timestamp('time_to_complete')->nullable();
    $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    $table->timestamps();
});
```

> Run `php artisan migrate` to create all tables automatically.

---

## ✨ Features

- User Authentication (Register, Login, Logout) via Laravel Breeze
- Create, Edit, Delete Tasks
- View tasks by category: Today, Upcoming, Completed
- Responsive design with Tailwind CSS
- Component-based Vue.js structure (Header, Footer, Modal)
- List rendering for tasks

---

## 👩‍💻 Author

**Nursyazwani Izzati Binti Mohd Salehin**
Full Stack Web Developer Application Assessment R2
