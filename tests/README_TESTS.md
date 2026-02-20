# Bestandslogik Tests

Umfassende Unit- und Feature-Tests für die Bestandsverwaltung mit Fokus auf Edge-Cases und Grenzbedingungen.

## Test-Dateien

### Unit Tests: `tests/Unit/StockAdjustmentTest.php`
Testet die Business-Logik der `StockAdjustment`-Action isoliert.

### Feature Tests: `tests/Feature/StockAdjustmentFeatureTest.php`
Testet die API-Endpunkte und die Integration mit der Datenbank.

## Getestete Edge-Cases

### 🔴 Untergrenze (0 - Negativer Bestand)

1. **Exakte Null**: Bestand von 50 auf exakt 0 reduzieren ✅
2. **Ein unter Null**: Bestand von 50 auf -1 reduzieren ❌ (verhindert)
3. **Von Null ins Negative**: Bestand von 0 auf -1 reduzieren ❌ (verhindert)
4. **Große negative Anpassung**: Bestand von 10 um -100 reduzieren ❌ (verhindert)
5. **Boundary 1 zu 0**: Bestand von 1 auf 0 reduzieren ✅

### 🔵 Obergrenze (100 - Maximaler Bestand)

1. **Exakt auf 100**: Bestand von 80 auf exakt 100 erhöhen ✅
2. **Ein über 100**: Bestand von 100 auf 101 erhöhen ❌ (verhindert)
3. **Von unter 100 über 100**: Bestand von 50 auf 101 erhöhen ❌ (verhindert)
4. **Boundary 99 zu 100**: Bestand von 99 auf 100 erhöhen ✅
5. **Bei Maximum bleiben**: Bestand ist 100, +1 hinzufügen ❌ (verhindert)

### 🟢 Produktvalidierung

1. **Produktname stimmt überein**: Erfolgreiche Anpassung ✅
2. **Produktname stimmt nicht überein**: Anpassung verhindert ❌
3. **Produkt existiert nicht**: Anpassung verhindert ❌

### 🟡 Produkt-Erstellung

1. **Produkt mit 0 Bestand erstellen**: Erlaubt ✅
2. **Produkt mit 100 Bestand erstellen**: Erlaubt ✅
3. **Produkt mit 101 Bestand erstellen**: Verhindert ❌

## Tests ausführen

### Alle Tests ausführen
```powershell
php artisan test
```

### Nur Unit Tests
```powershell
php artisan test --testsuite=Unit
```

### Nur Feature Tests
```powershell
php artisan test --testsuite=Feature
```

### Spezifische Test-Datei
```powershell
php artisan test tests/Unit/StockAdjustmentTest.php
php artisan test tests/Feature/StockAdjustmentFeatureTest.php
```

### Mit Coverage (falls Xdebug/PCOV installiert)
```powershell
php artisan test --coverage
```

### Einzelnen Test ausführen
```powershell
php artisan test --filter=it_prevents_negative_stock_edge_case_minus_one
```

## Test-Struktur

```
tests/
├── Unit/
│   └── StockAdjustmentTest.php       # 20 Unit Tests
├── Feature/
│   └── StockAdjustmentFeatureTest.php # 18 Feature Tests
└── README_TESTS.md                    # Diese Datei
```

## Erwartete Ergebnisse

Alle Tests sollten grün sein ✅

Gesamtanzahl: **38 Tests**
- Unit Tests: 20
- Feature Tests: 18

## Test-Abdeckung

Die Tests decken ab:
- ✅ Positive Bestandsanpassungen
- ✅ Negative Bestandsanpassungen
- ✅ Grenzbedingungen bei 0
- ✅ Grenzbedingungen bei 100
- ✅ Produktvalidierung (Name, ID)
- ✅ Autorisierung
- ✅ Fehlerbehandlung
- ✅ Datenbank-Transaktionen

## Wichtige Hinweise

1. **RefreshDatabase**: Tests verwenden `RefreshDatabase`, d.h. die Testdatenbank wird vor jedem Test zurückgesetzt
2. **Factories**: Tests nutzen Model Factories für konsistente Testdaten
3. **Authentifizierung**: Feature Tests testen mit verschiedenen Benutzerrollen (admin, staff)
4. **Isolation**: Unit Tests testen die Business-Logik isoliert, Feature Tests testen das Gesamtsystem

## Bei Fehlern

1. Datenbank-Migration prüfen: `php artisan migrate:fresh`
2. Cache leeren: `php artisan config:clear`
3. Autoload aktualisieren: `composer dump-autoload`
4. Testdatenbank konfigurieren in `.env.testing` oder `phpunit.xml`
