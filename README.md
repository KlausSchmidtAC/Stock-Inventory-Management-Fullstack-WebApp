# WWM - Laravel Developer Assessment 🚀

### 🧩 Szenario

Ihr Kunde, TechGear, ist ein Elektronik-Einzelhändler, der seine Abläufe modernisieren möchte. Sie wurden beauftragt, ein funktionales Inventar-Management-System in Laravel zu entwickeln. Der Fokus liegt dabei auf einer interaktiven Benutzeroberfläche (mit Livewire) und einer sauberen, modularisierten Geschäftslogik (mit Laravel Actions).

Der Kunde verkauft elektronische Produkte, die in Kategorien organisiert sind. Mitarbeiter haben unterschiedliche Rollen (Admin, Manager, Staff).

---

### ✅ Ihre Aufgabe

#### **Teil 1: Design & Planung**

* Erstellen Sie ein **vereinfachtes ERM-Diagramm** mit folgenden Entities:

  * `User`
  * `Role`
  * `Product`
  * `Category`

* Planen Sie Ihre **Laravel Actions** und **Livewire Components**:

  * Fokus auf CRUD für `Product` und `Category`
  * Authentifizierte User können je nach Rolle unterschiedliche Aktionen ausführen

* Notieren Sie Architekturüberlegungen **in einem kurzen Textdokument** (max. 1 Seite)

---

#### **Teil 2: Implementation**

* Implementieren Sie die Logik ausschließlich mit **[`lorisleiva/laravel-actions`](https://www.laravelactions.com/2.x/basic-usage.html#running-as-an-object)**.
* Die Benutzeroberfläche (UI) soll mit **Laravel Livewire** umgesetzt werden.

* Implementieren Sie die folgenden **Funktionalitäten**:

  * CRUD für `Product` und `Category` (In Livewire Components)
  * Zugriffskontrolle (z. B. nur Admin darf Produkte erstellen) unter Verwendung von Policies/Actions
  * Validierung innerhalb der Actions

* Verwenden Sie eine saubere **Fehlerbehandlung** und User-Feedback in der UI

* Schreiben Sie **Tests für die Actions** (Unit/Feature Tests)

* Dokumentieren Sie die Architektur grob:

  * Kurze Beschreibung, wie Actions und Livewire zusammenspielen.

---

### 🧹 Optional (falls noch Zeit bleibt)

* `InventoryTransaction`-Modell zur Nachverfolgung von Produktmengen
* Ein zusätzlicher Filter für Produkte in der Livewire-Übersicht
* Dark Mode Support für die UI

---

### 💡 Technische Hinweise

* **Tailwind CSS 4**: Das Projekt nutzt Tailwind v4. Eine `tailwind.config.js` ist nicht mehr nötig. Konfigurationen (z. B. Themes oder Custom Utilities) erfolgen direkt in der `resources/css/app.css`.
* **Livewire Layout**: Ein Basis-Layout für Full-Page-Components wurde unter `resources/views/components/layouts/app.blade.php` vorbereitet.
* **Datenbank**: Das Projekt ist für **SQLite** vorkonfiguriert. Sie müssen lediglich `php artisan migrate` ausführen (die Datei `database/database.sqlite` wird automatisch erstellt).
* **Server**: Nutzen Sie `php artisan serve`, um die Applikation lokal zu starten.
* **Vite**: Starten Sie den Asset-Bundler mit `npm run dev`.

---

### 🎯 Ziel

> **Liefern Sie eine kleine, wartbare Laravel-Applikation mit Fokus auf Codequalität, Struktur und der Nutzung von Laravel Actions sowie Livewire.** Umfang und Tiefe sind bewusst begrenzt – Qualität vor Vollständigkeit.

Viel Erfolg! 🍀
