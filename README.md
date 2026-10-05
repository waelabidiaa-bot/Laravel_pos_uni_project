# 🧾 Laravel POS System

A modern **Point of Sale (POS) system** built with **Laravel**, designed to manage products, categories, stock, cash sessions, sales, receipts, users, and store settings.

The project provides separate experiences for **Administrators** and **Cashiers**, with role-based access to the system.

---

## ✨ Features

### 🔐 Authentication & Roles

* User authentication
* Role-based access control
* Administrator and Cashier roles
* Protected routes and actions

### 📦 Product Management

* Create, edit and delete products
* Product categories
* Product pricing
* Stock management
* Product information management

### 🏷️ Category Management

* Create and manage categories
* Assign products to categories
* Category information and organization

### 📊 Stock Management

* Monitor product stock
* Track inventory levels
* Manage available quantities

### 💰 Point of Sale

* Create sales
* Add products to a sale
* Calculate totals
* Process payments
* Calculate change
* Generate receipts

### 🧾 Receipts

* Printable receipts
* Store information
* Cashier information
* Sale details
* Payment information
* Currency support

### 💵 Cash Sessions

* Cashier sessions
* Session management
* Track cashier activity
* Connect sales with cash sessions

### 📈 Statistics Dashboard

The administrator can view key sales metrics:

* Total revenue
* Total sales
* Products sold
* Average sale value
* Sales performance overview

### ⚙️ Store Settings

Administrators can configure:

* Store name
* Address
* Phone number
* Email
* Currency

### 🎨 Appearance

* Light theme
* Dark theme
* Theme preference saved per user
* Responsive interface

---

## 🛠️ Technologies

| Technology       | Purpose             |
| ---------------- | ------------------- |
| **Laravel**      | Backend framework   |
| **PHP**          | Backend programming |
| **MySQL**        | Database            |
| **Blade**        | Templating          |
| **Tailwind CSS** | UI styling          |
| **Font Awesome** | Icons               |
| **JavaScript**   | Interactive UI      |

---

## 🏗️ Architecture

The application follows the Laravel MVC architecture:

```text
Laravel POS
│
├── Models
│   ├── User
│   ├── Product
│   ├── Category
│   ├── Sale
│   ├── SaleItem
│   ├── Payment
│   ├── CashSession
│   └── Parametre
│
├── Controllers
│   ├── Authentication
│   ├── Admin
│   ├── Cashier
│   └── Settings
│
├── Views
│   ├── Admin
│   ├── Cashier
│   ├── Products
│   ├── Categories
│   ├── Sales
│   ├── Statistics
│   └── Settings
│
└── Database
    ├── Migrations
    └── Seeders
```

---

## 👥 User Roles

### 👨‍💼 Administrator

Administrators have access to the management side of the POS.

They can:

* Manage products
* Manage categories
* Manage stock
* Manage cashiers
* View sales
* View statistics
* Configure store settings
* Manage their appearance preferences

### 🧑‍💻 Cashier

Cashiers have access to the sales side of the system.

They can:

* Start/manage their cash session
* Create sales
* Process payments
* Print receipts
* View their session information
* Change their appearance preferences

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
cd YOUR_REPOSITORY
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create the environment file

```bash
cp .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Configure the database

Edit your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_laravel_project
DB_USERNAME=root
DB_PASSWORD=
```

Use your own database credentials.

### 7. Run migrations

```bash
php artisan migrate
```

If the project contains seeders:

```bash
php artisan db:seed
```

### 8. Start the development server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## 🎨 Frontend

The interface uses **Tailwind CSS** to create a clean and responsive POS dashboard.

The UI focuses on:

* Clear navigation
* Responsive layouts
* Dashboard cards
* Consistent spacing
* Accessible controls
* Light and dark themes
* Simple POS workflows

---

## 📊 Dashboard

The administrator dashboard provides access to the main management areas:

```text
Dashboard
│
├── Products
├── Categories
├── Stock
├── Cashiers
├── Sales
├── Statistics
└── Settings
```

---

## 🔒 Security

The application uses Laravel's built-in security features, including:

* Authentication
* CSRF
