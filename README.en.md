
# TechGear Inventory Management System

## Scenario

TechGear is a consumer electronics retailer aiming to modernize its operations. This project is a functional inventory management system built with Laravel, featuring an interactive user interface (Livewire) and modular business logic (Laravel Actions).

The system manages electronic products organized into categories. Employees have different roles (Admin, Manager, Staff).

---

## Features

- **Modern UI with Livewire**
  - Dynamic switching between table and badge views for categories
  - Instant display of stock changes and product details
  - Dark mode support

- **Product and Category Management**
  - CRUD for products and categories
  - Display of all categories with statistics (total count, low stock, out of stock)
  - Direct linking from categories to filtered product views

- **Stock Management**
  - Stock adjustment (inbound/outbound) with validation (no negative stock)
  - Overview of products with low or no stock

- **Role and Permission System**
  - Different permissions for Admin, Manager, and Staff
  - Policy-based access control

- **Transparent History**
  - InventoryTransaction model for tracking stock changes

- **Technical Implementation**
  - Use of Laravel Actions for scalable business logic
  - Clean user feedback and error handling in the UI
  - Database transactions for critical operations
  - SQLite as preconfigured database
  - Tailwind CSS 4 for modern design
  - Vite for asset bundling

- **Testing**
  - Unit and feature tests for stock logic and edge cases

---

## Setup

```bash
composer install
npm install && npm run dev
php artisan migrate
php artisan serve
```

---

Enjoy exploring the app!

Good luck! 🍀
