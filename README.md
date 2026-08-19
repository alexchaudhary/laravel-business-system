# Laravel Business System

A full-featured business management system built with Laravel for managing customers, suppliers, products, purchases, sales, inventory, expenses, invoices, payments, reports, and users.

The project focuses on real-world business workflows, role-based authorization, inventory/stock calculations, financial transactions, and automated business-logic testing.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP 8.4
- **Frontend:** Blade, Tailwind CSS
- **Database:** MySQL
- **Build Tool:** Vite
- **Authentication:** Laravel Breeze
- **Testing:** PHPUnit / Laravel Feature Tests
- **Version Control:** Git & GitHub

## 🚀 Features

### 🔐 Authentication & Authorization
- User registration and login
- Logout
- Email verification
- Password reset
- Password confirmation
- Profile management
- Role-based access control
- Admin and normal-user permissions

### 👥 Customer Management
- Create customers
- View customer details
- Edit customers
- Delete customers
- Customer validation

### 🏢 Supplier Management
- Create suppliers
- View supplier details
- Edit suppliers
- Delete suppliers
- Supplier validation
- Active/inactive supplier status

### 📦 Product Management
- Create products
- View product details
- Edit products
- Delete products
- SKU management
- Unique SKU validation
- Purchase price and selling price
- Stock quantity
- Low-stock threshold
- Product activation/deactivation

### 🛒 Purchase Management
- Create purchases
- View purchases
- Edit purchases
- Delete purchases
- Multiple products per purchase
- Purchase status management
- Automatic stock updates
- Stock restoration when purchases are cancelled/deleted

### 💰 Sales Management
- Create sales
- View sales
- Edit sales
- Delete sales
- Multiple products per sale
- Stock availability validation
- Automatic stock deduction
- Stock restoration when sales are cancelled/deleted

### 📊 Inventory Management
- Current stock tracking
- Total stock units
- Stock value calculation
- Low-stock detection
- Product-specific low-stock thresholds
- Inventory overview

### 🔧 Stock Adjustments
- Increase stock
- Decrease stock
- Prevent stock from becoming negative
- Adjustment reason
- Stock adjustment history
- Admin-only stock adjustments

### 💸 Expense Management
- Create expenses
- View expenses
- Edit expenses
- Delete expenses
- Expense categories
- Amount validation
- Date and description handling

### 🧾 Invoice Management
- Create invoices
- View invoices
- Edit invoices
- Delete invoices
- Invoice number uniqueness
- Invoice status management
- Due-date validation
- Invoice/sale relationship

### 💳 Payment Management
- Create payments
- View payments
- Edit payments
- Delete payments
- Payment number uniqueness
- Payment method validation
- Partial payments
- Full payments
- Automatic invoice status recalculation

### 📈 Reports
- Sales reports
- Purchase reports
- Expense reports
- Profit calculation
- Date filtering
- From/to date validation
- Cancelled transactions excluded from calculations

### 🛡️ Admin Panel
- Admin dashboard
- User management
- Role management
- Business statistics
- Recent sales
- Recent purchases
- Recent invoices
- Recent payments
- Authorization checks

---

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/alexchaudhary/laravel-business-system.git
cd laravel-business-system
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create environment file

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Configure the database

Update the database settings in `.env` according to your local MySQL configuration.

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Install frontend dependencies

```bash
npm install
```

### 8. Build frontend assets

```bash
npm run build
```

### 9. Start the development server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## 🧪 Testing

The project includes automated feature/business-logic tests covering authentication, authorization, CRUD operations, inventory calculations, stock movements, invoices, payments, sales, purchases, reports, and admin functionality.

### Current Test Result

```text
Tests:       215 passed
Assertions:  644
Failures:      0
```