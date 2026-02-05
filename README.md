# WWM - Laravel Developer Assessment 🚀

### 🧩 Szenario

Ihr Kunde, TechGear, ist ein Elektronik-Einzelhändler, der seine Abläufe modernisieren möchte. Sie wurden beauftragt, ein funktionales Inventar-Management-System in Laravel zu entwickeln. Der Fokus liegt dabei auf einer interaktiven Benutzeroberfläche (mit Livewire) und einer sauberen, modularisierten Geschäftslogik (mit Laravel Actions).

Der Kunde verkauft elektronische Produkte, die in Kategorien organisiert sind. Mitarbeiter haben unterschiedliche Rollen (Admin, Manager, Staff).

---

### ✅ Ihre Aufgabe

> **Zeitansatz:** ca. 4 Stunden

#### **Teil 1: Design & Planung**

* Erstellen Sie ein **fokussiertes ERM-Diagramm** für die Inventory Domain:
  * Entities: `Product`, `Category`, `Stock` (oder Bestands-Attribut) & Rollenbezug.
* Planen Sie Ihre **Laravel Actions** und **Livewire Components**:
  * Fokus auf CRUD für `Product` und eine Logik zur **Bestandsanpassung** (Stock Adjustment).
  * Rollenkonzept: Admin (Vollzugriff) vs. Staff (eingeschränkt).

---

#### **Teil 2: Implementation**

* Implementieren Sie die Logik mit **[`lorisleiva/laravel-actions`](https://www.laravelactions.com/2.x/basic-usage.html#running-as-an-object)**.
* Die Benutzeroberfläche (UI) soll mit **Laravel Livewire** umgesetzt werden.

* Implementieren Sie die folgenden Anforderungen:
  * **Product Management**: CRUD-Funktionalität für Produkte.
  * **Stock Adjustment**: Eine Action zur Bestandsänderung (z. B. Wareneingang/-ausgang) mit Validierung (kein negativer Bestand).
  * **Robustheit**: Einsatz von Database Transactions für Bestandsänderungen und Policy-basierten Zugriffsschutz.
* Verwenden Sie sauberes User-Feedback und Fehlerbehandlung in der UI.
* Schreiben Sie **Unit/Feature Tests** für die Bestandslogik (Edge-Cases berücksichtigen).

---

### 🧹 Optional (falls noch Zeit bleibt)

* `InventoryTransaction`-Modell zur detaillierten Historisierung
* Kurze schriftliche Begründung für das "Laravel Actions" Pattern (Skalierbarkeit)
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

> **Liefern Sie eine kleine, wartbare Laravel-Applikation mit Fokus auf Codequalität und Struktur. Die Bearbeitungszeit sollte ca. 4 Stunden betragen.** Qualität vor Vollständigkeit.

Viel Erfolg! 🍀
