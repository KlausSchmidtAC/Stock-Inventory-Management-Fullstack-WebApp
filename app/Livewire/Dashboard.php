<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\GetProductsByCategory;
use App\Actions\GetProduct;
use App\Actions\GetCategory;
use App\Actions\GetOutOfStockProducts;
use App\Actions\GetProductsCountLessTen;
use App\Actions\CreateProduct;
use App\Actions\CreateCategory;
use App\Actions\UpdateProduct;
use App\Actions\DeleteProduct;
use App\Actions\DeleteCategory;
use App\Actions\GetCategories;
use App\Actions\StockAdjustment;
use Livewire\Attributes\Layout;


#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public string $selectedAction = '';

    // Input fields for different actions
    public $productId = '';
    public $categoryId = '';
    public $categoryName = '';
    public $categoryNameForCreate = '';
    public $name = '';
    public $productName = '';
    public $price = '';
    public $categoryIdForCreate = '';
    public $stockQuantity = '';
    public $adjustment = '';
    public $ISBN = '';
    public $supplier = '';
    public $manufacturer = '';

    public $showCategoriesComponent = false;

    // Response data
    public $result = null;
    public $error = null;

    public function mount()
    {
        if (request()->has('selectedAction') && request()->has('categoryId')) {
            $this->selectedAction = request()->get('selectedAction', '');
            $categoryIdFromRequest = request()->get('categoryId');
            $categoryNameFromRequest = request()->get('categoryName');


            if ($this->selectedAction === 'get-by-category' && is_numeric($categoryIdFromRequest)) {
                $this->categoryId = $categoryIdFromRequest;
                $this->categoryName = $categoryNameFromRequest;
                $this->executeAction();
            }
        } else {

            $this->reset(['result', 'error']);
        }
    }

    public function updatedSelectedAction()
    {
        $this->reset(['result', 'error', 'productId', 'categoryId', 'categoryName', 'ISBN', 'supplier', 'manufacturer', 'categoryNameForCreate', 'name', 'productName', 'price', 'categoryIdForCreate', 'stockQuantity', 'adjustment']);
    }

    public function executeAction()
    {
        $this->reset(['result', 'error']);

        try {
            switch ($this->selectedAction) {
                case 'get-by-category':
                    // Validierung: entweder ID oder Name muss angegeben werden
                    if (empty($this->categoryId) && empty($this->categoryName)) {
                        $this->error = 'Bitte geben Sie entweder eine Kategorie-ID und/oder einen Kategorienamen ein.';
                        break;
                    }

                    if (!empty($this->categoryId)) {
                        if (!preg_match('/^\d+$/', trim($this->categoryId))) {
                            $this->error = 'Kategorie-ID muss eine gültige Ganzzahl sein.';
                            break;
                        }
                        $action = new GetCategory();
                        $categoryById = $action->handle($this->categoryId);
                        if ($categoryById->name !== $this->categoryName) {
                            $this->error = 'Kategoriename "' . $this->categoryName . '" stimmt nicht mit der Kategorie ID ' . $this->categoryId . ' überein. Gespeicherter Name: "' . $categoryById->name . '"';
                            break;
                        }
                    }

                    if (!empty($this->categoryName)) {
                        $action = new GetCategory();
                        $categoryByName = $action->handleByName($this->categoryName);
                        if ($categoryByName->id != $this->categoryId) {
                            $this->error = 'Kategorie-ID "' . $this->categoryId . '" stimmt nicht mit der Kategorie "' . $this->categoryName . '" überein. Gespeicherte ID: "' . $categoryByName->id . '"';
                            break;
                        }
                    }

                    // Produkte in dieser Kategorie laden
                    $action = new GetProductsByCategory();
                    $response = $action->handle($this->categoryId);
                    $this->result = $response->getData();

                    // Kategorie existiert, aber keine Produkte
                    if (is_array($this->result) && empty($this->result)) {
                        $this->error = 'Keine Produkte in der Kategorie "' . $this->categoryName . '" (ID: ' . $this->categoryId . ') gefunden.';
                    }
                    break;

                case 'get-product':
                    // Validierung: mindestens ID oder Name muss angegeben werden
                    if (empty($this->productId) && empty($this->productName)) {
                        $this->error = 'Bitte geben Sie entweder eine Produkt-ID oder einen Produktnamen ein.';
                        break;
                    }

                    // Beide Felder ausgefüllt: Prüfen ob ID und Name zum selben Produkt gehören
                    if (!empty($this->productId) && !empty($this->productName)) {
                        if (!preg_match('/^\d+$/', trim($this->productId))) {
                            $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                            break;
                        }

                        $action = new GetProduct();
                        $this->result = $action->handleByIdAndName($this->productId, $this->productName);
                        break;
                    }

                    // Nur ein Feld ausgefüllt: Normale Suche
                    $action = new GetProduct();


                    if (!empty($this->productId)) {
                        if (!preg_match('/^\d+$/', trim($this->productId))) {
                            $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                            break;
                        }
                        $this->result = $action->handle($this->productId);
                    } else {
                        $this->result = $action->handleByName($this->productName);
                    }
                    break;

                case 'out-of-stock':
                    $action = new GetOutOfStockProducts();
                    $response = $action->handle();
                    $products = $response->getData();

                    // Prüfen ob keine nicht-vorrätigen Produkte gefunden wurden
                    if (is_array($products) && empty($products)) {
                        $this->result = null;
                        $this->error = 'Keine nicht-vorrätigen Produkte in einer Produktkategorie gefunden. Alle Produkte sind auf Lager!';
                    } else {
                        $this->result = $products;
                    }
                    break;

                case 'low-stock-products':
                    $action = new GetProductsCountLessTen();
                    $response = $action->handle();
                    $products = $response->getData();

                    // Prüfen ob keine Produkte mit weniger als 10 Stück gefunden wurden
                    if (is_array($products) && empty($products)) {
                        $this->result = null;
                        $this->error = 'Keine Produkte mit weniger als 10 Stück gefunden. Alle Produkte sind gut bevorratet!';
                    } else {
                        $this->result = $products;
                    }
                    break;

                case 'create-product':
                    if (empty($this->categoryIdForCreate) || !preg_match('/^\d+$/', trim($this->categoryIdForCreate))) {
                        $this->error = 'Kategorie-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }
                    if (empty($this->stockQuantity) || !preg_match('/^\d+$/', trim($this->stockQuantity))) {
                        $this->error = 'Anfangsbestand muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    // Prüfe ob Anfangsbestand die Obergrenze von 100 überschreitet
                    if ($this->stockQuantity > 100) {
                        $this->error = 'Anfangsbestand darf die Obergrenze von 100 nicht überschreiten.';
                        break;
                    }

                    // Prüfe ob Preis positiv ist
                    if (empty($this->price) || !is_numeric($this->price) || $this->price < 0) {
                        $this->error = 'Bitte einen positiven Betrag als Preis angeben!';
                        break;
                    }

                    // Prüfe ob ISBN numerisch ist
                    if (!empty($this->ISBN) && $this->isValidIsbn($this->ISBN) === false) {
                        $this->error = 'Bitte geben Sie eine gültige 10- oder 13-stellige ISBN-Nummer ein.';
                        break;
                    }

                    // Prüfe ob Kategorie existiert
                    $categoryAction = new GetCategory();
                    $categoryAction->handle($this->categoryIdForCreate);

                    $action = new CreateProduct();
                    $this->result = $action->handle([
                        'name' => $this->name,
                        'price' => $this->price,
                        'category_id' => $this->categoryIdForCreate,
                        'stock_quantity' => $this->stockQuantity,
                        'supplier' => $this->supplier,
                        'manufacturer' => $this->manufacturer,
                        'isbn' => $this->ISBN,
                    ]);
                    break;

                case 'create-category':
                    // Nur Admins und Manager dürfen Kategorien erstellen

                    if (empty($this->categoryNameForCreate)) {
                        $this->error = 'Bitte geben Sie einen Kategorienamen ein.';
                        break;
                    }

                    $action = new CreateCategory();
                    $this->result = $action->handle([
                        'name' => $this->categoryNameForCreate,
                    ]);
                    break;

                case 'delete-category':

                    // Validiere Kategorie-ID
                    if (empty($this->categoryId) || !preg_match('/^\d+$/', trim($this->categoryId))) {
                        $this->error = 'Kategorie-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    // Validiere Kategoriename
                    if (empty($this->categoryName)) {
                        $this->error = 'Bitte geben Sie den Kategorienamen zur Bestätigung ein.';
                        break;
                    }

                    $action = new GetCategory();
                    $category = $action->handle($this->categoryId);

                    // Prüfe ob eingegebener Name mit gespeichertem übereinstimmt
                    if ($category->name !== $this->categoryName) {
                        $this->error = 'Kategoriename "' . $this->categoryName . '" stimmt nicht mit der Kategorie ID ' . $this->categoryId . ' überein. Gespeicherter Name: "' . $category->name . '"';
                        break;
                    }

                    $action = new DeleteCategory();
                    $response = $action->handle($this->categoryId);
                    $this->result = $response->getData();
                    break;

                case 'update-product':
                    // Validiere Produkt-ID
                    if (empty($this->productId) || !preg_match('/^\d+$/', trim($this->productId))) {
                        $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    // Validiere Kategorie-ID
                    if (empty($this->categoryIdForCreate) || !preg_match('/^\d+$/', trim($this->categoryIdForCreate))) {
                        $this->error = 'Neue Kategorie-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    // Prüfe ob Preis positiv ist
                    if (empty($this->price) || !is_numeric($this->price) || $this->price < 0) {
                        $this->error = 'Bitte einen positiven Betrag als Preis angeben!';
                        break;
                    }

                    // Prüfe ob ISBN numerisch ist
                    if (!empty($this->ISBN) && $this->isValidIsbn($this->ISBN) === false) {
                        $this->error = 'Bitte geben Sie eine gültige 10- oder 13-stellige ISBN-Nummer ein.';
                        break;
                    }

                    // Prüfe ob Kategorie existiert
                    $categoryAction = new GetCategory();
                    $categoryAction->handle($this->categoryIdForCreate);

                    $updateAction = new UpdateProduct();
                    $this->result = $updateAction->handle($this->productId, [
                        'name' => $this->name,
                        'price' => $this->price,
                        'category_id' => $this->categoryIdForCreate,
                        'supplier' => $this->supplier,
                        'manufacturer' => $this->manufacturer,
                        'isbn' => $this->ISBN,
                    ]);
                    break;

                case 'delete-product':
                    // Validierung: BEIDE Felder müssen angegeben werden
                    if (empty($this->productId) || empty($this->productName)) {
                        $this->error = 'Bitte geben Sie sowohl Produkt-ID als auch Produktname ein.';
                        break;
                    }

                    // Prüfe ob ID eine gültige Ganzzahl ist
                    if (!preg_match('/^\d+$/', trim($this->productId))) {
                        $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    // Prüfen ob ID und Name zum selben Produkt gehören
                    $action = new GetProduct();
                    $productdata = $action->handleByIdAndName($this->productId, $this->productName);
                    $deleteAction = new DeleteProduct();
                    $deleteAction->handle($this->productId);

                    // Speichere Produktdaten für Anzeige (Produkt ist bereits gelöscht)
                    $this->result = [$productdata]; // Als Array für Tabellen-Anzeige
                    break;

                case 'stock-adjustment':
                    // Validierung: adjustment muss Format +Zahl oder -Zahl haben
                    if (empty($this->adjustment)) {
                        $this->error = 'Bestandsanpassung ist erforderlich.';
                        break;
                    }

                    // Prüfe ob Vorzeichen vorhanden ist und dahinter eine positive Ganzzahl folgt
                    if (!preg_match('/^[+\-]\d+$/', $this->adjustment)) {
                        $this->error = 'Bestandsanpassung muss mit + oder - beginnen, gefolgt von einer positiven Ganzzahl (z.B. +10 oder -5).';
                        break;
                    }

                    // Validierung: productId muss eine Ganzzahl sein
                    if (empty($this->productId) || !preg_match('/^\d+$/', trim($this->productId))) {
                        $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                        break;
                    }

                    $prodInfo = [
                        'product_id' => $this->productId,
                        'product_name' => $this->productName,
                        'adjustment' => (int)$this->adjustment,
                    ];
                    $action = new StockAdjustment();
                    $this->result = $action->handleStockAdjustment($prodInfo);
                    break;

                case 'view-categories':
                    $action = new GetCategories();
                    $this->result = $action->handle();
                    break;

                default:
                    $this->error = 'Bitte wählen Sie eine Aktion aus.';
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = 'Validierungsfehler: ' . collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Fehler: ' . $e->getMessage();
        }
    }

    function isValidIsbn($isbn)
    {
        $isbn = str_replace(['-', ' '], '', strtoupper($isbn));
        if (preg_match('/^\d{9}[\dX]$/', $isbn)) {
            // ISBN-10
            $sum = 0;
            for ($i = 0; $i < 9; $i++) $sum += ((int)$isbn[$i]) * (10 - $i);
            $check = $isbn[9] == 'X' ? 10 : (int)$isbn[9];
            $sum += $check;
            return $sum % 11 == 0;
        } elseif (preg_match('/^97[89]\d{10}$/', $isbn)) {
            // ISBN-13
            $sum = 0;
            for ($i = 0; $i < 12; $i++) $sum += ((int)$isbn[$i]) * ($i % 2 ? 3 : 1);
            $check = (10 - ($sum % 10)) % 10;
            return $check == (int)$isbn[12];
        }

        return false;
    }

    public function showCategories()
    {
        $this->showCategoriesComponent = !$this->showCategoriesComponent;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
