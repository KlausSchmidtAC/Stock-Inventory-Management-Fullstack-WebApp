# Stock-Inventory-Management-Fullstack-
Inventory-Management WebApp (Frontend/Backend: Laravel-Livewire)

# TechGear Inventory Management System

## Szenario

TechGear ist ein Elektronik-Einzelhändler, der seine Abläufe modernisieren möchte. Dieses Projekt ist ein funktionales Inventar-Management-System auf Basis von Laravel, mit einer interaktiven Benutzeroberfläche (Livewire) und einer modularen Geschäftslogik (Laravel Actions).

Das System verwaltet elektronische Produkte, die in Kategorien organisiert sind. Mitarbeiter haben unterschiedliche Rollen (Admin, Manager, Staff).

---

## Features

- **Moderne UI mit Livewire**
  - Dynamisches Umschalten zwischen Tabellen- und Badge-Ansicht für Kategorien
  - Sofortige Anzeige von Bestandsänderungen und Produktdetails

- **Produkt- und Kategorienverwaltung**
  - CRUD für Produkte und Kategorien
  - Anzeige aller Kategorien mit Statistiken (Gesamtanzahl, niedriger Bestand, nicht vorrätig)
  - Direkte Verlinkung von Kategorien zu gefilterten Produktansichten

- **Bestandsmanagement**
  - Bestandsanpassung (Wareneingang/-ausgang) mit Validierung (kein negativer Bestand)
  - Übersicht über Produkte mit niedrigem oder keinem Bestand

- **Rollen- und Berechtigungssystem**
  - Unterschiedliche Rechte für Admin, Manager und Staff
  - Policy-basierter Zugriffsschutz

- **Transparente Historie**
  - InventoryTransaction-Modell zur Nachverfolgung von Bestandsänderungen

- **Technische Umsetzung**
  - Nutzung von Laravel Actions für skalierbare Geschäftslogik
  - Sauberes User-Feedback und Fehlerbehandlung in der UI
  - Datenbank-Transaktionen für kritische Operationen
  - SQLite als vorkonfigurierte Datenbank
  - Tailwind CSS 4 für modernes Design
  - Vite für Asset-Bundling

- **Testing**
  - Unit- und Feature-Tests für die Bestandslogik und Edge Cases

---

## Setup

```bash
composer install
npm install && npm run dev
php artisan migrate
php artisan serve
```

---

Viel Spaß beim Ausprobieren!
