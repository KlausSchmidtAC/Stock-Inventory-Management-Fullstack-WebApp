# TechGear Inventory Management System
### ⚠️ DEMO & TESTING PROJECT DISCLAIMER
This repository is created **strictly for testing and demonstration purposes**. It does not feature production-grade integrations, such as external OAuth single sign-on (SSO) servers, cloud-managed identity providers, or enterprise authentication services. All mechanisms run locally for easy auditing and evaluation.

---

## Scenario
TechGear is an electronics retailer looking to modernize its operations. This project delivers a functional, full-stack Inventory Management System built on Laravel, combining an interactive frontend user interface (Livewire) with highly modular, isolated business logic (Laravel Actions).

The system manages electronic products organized into distinct categories, enforcing robust access control tailored to user roles (Admin, Manager, Staff).

---

## Features

- **Modern UI via Livewire**
  - Instant toggle switches between structural HTML tables and dynamic card/badge views for categories.
  - Live state updates for stock alterations and comprehensive product information without manual page refreshes.

- **Product & Category Management**
  - Fully featured CRUD operations for items and groups.
  - Granular category analytics reporting total item counts, low stock alerts, and out-of-stock deficits.
  - Contextual link redirection bridging specific categories directly to pre-filtered product lists.

- **Robust Stock Control**
  - Validated inventory operations (inbound/outbound shipments) preventing negative balances or over-capacity constraints.
  - Fast-filtering dashboards targeting depleted or critically low-stock items.

- **Role-Based Access Control (RBAC)**
  - Matrix-driven authorization restrictions mapping distinct actions specifically to Admin, Manager, and Staff roles.
  - Strong server-side guardrails managed strictly via Laravel Policies and Gates.

- **Audit & Transaction History**
  - Automated `InventoryTransaction` logging to audit old vs. new stock quantities, operation reasons, timestamps, and active user IDs.

- **Technical Implementations**
  - Engineered using the **Action Pattern** utilizing dynamic request routing mappings for increased code reuse and enterprise scalability.
  - Atomic database transactions guaranteeing integrity across multi-table writes during mutations.
  - Zero-config local sqlite driver out of the box.
  - Styled utilizing Tailwind CSS.
  - Asset bundling handled asynchronously by Vite.

- **Automated Testing**
  - Suite of Unit and Feature tests enforcing business rules, authorization blocks, and validation edge cases.

---

## Architectural Highlight: The Action Pattern & Dynamic Mapping
To bypass monolithic controllers or messy switch-case route structures inside components, this system uses encapsulated **Action Classes** (Single-Action Controllers) invoked dynamically by a central registry in the Dashboard.

### 1. Unified Request Handling
The Livewire controller maintains a lightweight lookup map binding frontend request keys directly to dedicated handler methods:
```php
protected array \$actionMethodsMapping = [
    'get-product'      => 'handleGetProduct',
    'stock-adjustment' => 'handleStockAdjustment',
    'create-product'   => 'handleCreateProduct', ...
];
```

### 2. Dependency Resolution via Container
When a form submits via `wire:submit.prevent="executeAction"`, Laravel's Service Container inspects the mapped method signature to resolve, validate, and inject the precise Action class on the fly:
```php
\$methodName = \(this->actionMethodsMapping[\)this->selectedAction];
\(this->result = app()->call([\)this, \(methodName],\)this->formData);
```

### 3. Benefits of this Architecture
* **High Maintainability**: New business features are added simply by spawning a standalone Action class and registering it in the mapping array.
* **Encapsulated Security**: Validation schemas, authorization rules (`Gate::authorize()`), and database transactions are locked into individual, self-contained files.
* **Clean Dashboards**: The main Livewire controller serves solely as a presentation manager rather than a hub for complex data processing.

---

## Demo Test Credentials
You can log into the local development server immediately using the following pre-seeded mock users:

| Role / Permissions | Email Address | Cleartext Password |
| :--- | :--- | :--- |
| **Standard User / Staff** | `test@example.com` | `12345` |
| **Administrator / Manager** | `admin@example.com` | `admin123` |

---

## Setup & Local Installation

Follow these steps to spin up the local development suite:

1. **Install Composer Dependencies**
   ```bash
   composer install
   ```

2. **Install Asset Bundles & Spin Up Vite Server**
   ```bash
   npm install
   ```
   ```bash
   npm run dev
   ```

3. **Initialize Database and Demo Data**
   *(Ensure your database config points to the pre-allocated SQLite database)*
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Launch Local Application Server**
   ```bash
   php artisan serve
   ```
   Open your browser and navigate to the default address: `http://localhost:8000`

---
Have fun testing the environment!