# WWM - Laravel Developer Assessment 🚀

### 🧩 Scenario

Your client, TechGear, is an electronics retailer looking to modernize its operations. You have been tasked with developing a functional inventory management system in Laravel. The focus is on an interactive user interface (using Livewire) and clean, modularized business logic (using Laravel Actions).

The client sells electronic products organized into categories. Employees have different roles (Admin, Manager, Staff).

---

### ✅ Your Task

> **Estimated Time:** approx. 4 hours

#### **Part 1: Design & Planning**

* Create a **focused ERM diagram** for the Inventory Domain:
  * Entities: `Product`, `Category`, `Stock` (or stock attribute) & Role context.
* Plan your **Laravel Actions** and **Livewire Components**:
  * Focus on CRUD for `Product` and logic for **Stock Adjustment**.
  * Role concept: Admin (Full access) vs. Staff (restricted).

---

#### **Part 2: Implementation**

* Implement the logic using **[`lorisleiva/laravel-actions`](https://www.laravelactions.com/2.x/basic-usage.html#running-as-an-object)**.
* The user interface (UI) should be implemented with **Laravel Livewire**.

* Implement the following requirements:
  * **Product Management**: CRUD functionality for products.
  * **Stock Adjustment**: An Action to modify stock levels (e.g., stock in/out) with validation (prevent negative stock).
  * **Robustness**: Use Database Transactions for stock changes and Policy-based authorization.
* Use clean user feedback and error handling in the UI.
* Write **Unit/Feature Tests** for the stock logic (considering edge cases).

---

### 🧹 Optional (if time permits)

* `InventoryTransaction` model for detailed history tracking
* Brief written justification for the "Laravel Actions" pattern (scalability)
* Dark Mode support for the UI

---

### 💡 Technical Hints

* **Tailwind CSS 4**: This project uses Tailwind v4. A `tailwind.config.js` is no longer needed. Configurations (e.g., themes or custom utilities) are handled directly in `resources/css/app.css`.
* **Livewire Layout**: A base layout for full-page components is provided at `resources/views/components/layouts/app.blade.php`.
* **Database**: The project is pre-configured for **SQLite**. Simply run `php artisan migrate` (the `database/database.sqlite` file will be created automatically).
* **Server**: Use `php artisan serve` to start the application locally.
* **Vite**: Start the asset bundler using `npm run dev`.

---

### 🎯 Goal

> **Deliver a small, maintainable Laravel application with a focus on code quality and structure. The estimated time should be around 4 hours.** Quality over completeness.

Good luck! 🍀
