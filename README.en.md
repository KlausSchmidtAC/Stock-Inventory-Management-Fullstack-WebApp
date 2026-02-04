# WWM - Laravel Developer Assessment 🚀

### 🧩 Scenario

Your client, TechGear, is an electronics retailer looking to modernize its operations. You have been tasked with developing a functional inventory management system in Laravel. The focus is on an interactive user interface (using Livewire) and clean, modularized business logic (using Laravel Actions).

The client sells electronic products organized into categories. Employees have different roles (Admin, Manager, Staff).

---

### ✅ Your Task

#### **Part 1: Design & Planning**

* Create a **simplified ERM diagram** with the following entities:

  * `User`
  * `Role`
  * `Product`
  * `Category`

* Plan your **Laravel Actions** and **Livewire Components**:

  * Focus on CRUD for `Product` and `Category`
  * Authenticated users can perform different actions depending on their role

* Note your architectural considerations **in a short text document** (max. 1 page)

---

#### **Part 2: Implementation**

* Implement the logic exclusively using **[`lorisleiva/laravel-actions`](https://www.laravelactions.com/2.x/basic-usage.html#running-as-an-object)**.
* The user interface (UI) should be implemented with **Laravel Livewire**.

* Implement the following **functionalities**:

  * CRUD for `Product` and `Category` (In Livewire Components)
  * Access control (e.g., only Admin can create products) using Policies/Actions
  * Validation within the Actions

* Use clean **error handling** and user feedback in the UI

* Write **tests for the Actions** (Unit/Feature Tests)

* Briefly document the architecture:

  * Short description of how Actions and Livewire work together.

---

### 🧹 Optional (if time permits)

* `InventoryTransaction` model for tracking product quantities
* An additional filter for products in the Livewire overview
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

> **Deliver a small, maintainable Laravel application with a focus on code quality, structure, and the use of Laravel Actions and Livewire.** Scope and depth are deliberately limited – quality over completeness.

Good luck! 🍀
