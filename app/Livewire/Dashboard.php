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
use App\Actions\StockAdjustment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;

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
    
    // Response data
    public $result = null;
    public $error = null;

    public function mount()
    {
        $this->reset(['result', 'error']);
    }

    public function updatedSelectedAction()
    {
        $this->reset(['result', 'error', 'productId', 'categoryId', 'categoryName', 'categoryNameForCreate', 'name', 'productName', 'price', 'categoryIdForCreate', 'stockQuantity', 'adjustment']);
    }

    public function executeAction()
    {
        $this->reset(['result', 'error']);
        
        try {
            switch ($this->selectedAction) {
                case 'get-by-category':
                    // Validierung: entweder ID oder Name muss angegeben werden
                    if (empty($this->categoryId) && empty($this->categoryName)) {
                        $this->error = 'Bitte geben Sie entweder eine Kategorie-ID oder einen Kategorienamen ein.';
                        break;
                    }
                    
                    if (!empty($this->categoryId) && !empty($this->categoryName)) {
                        $this->error = 'Bitte geben Sie nur eine Kategorie-ID ODER einen Kategorienamen ein, nicht beides.';
                        break;
                    }
                    
                    // Kategorie suchen
                    $category = null;
                    $searchTerm = '';
                    $searchType = '';
                    
                    if (!empty($this->categoryId)) {
                        if (!preg_match('/^\d+$/', trim($this->categoryId))) {
                            $this->error = 'Kategorie-ID muss eine gültige Ganzzahl sein.';
                            break;
                        }
                        $this->validate(['categoryId' => 'integer']);
                        $action = new GetCategory();
                        $response = $action->handle($this->categoryId);
                        
                        if ($response->status() === 404) {
                            $this->error = 'Kategorie mit ID "' . $this->categoryId . '" existiert nicht.';
                            break;
                        }
                        
                        $category = $response->getData();
                        $searchTerm = $this->categoryId;
                        $searchType = 'ID';
                    } else {
                        $this->validate(['categoryName' => 'string']);
                        $action = new GetCategory();
                        $response = $action->handleByName($this->categoryName);
                        
                        if ($response->status() === 404) {
                            $this->error = 'Kategorie mit Name "' . $this->categoryName . '" existiert nicht.';
                            break;
                        }
                        
                        $category = $response->getData();
                        $searchTerm = $this->categoryName;
                        $searchType = 'Name';
                    }
                    
                    // Produkte in dieser Kategorie laden
                    $action = new GetProductsByCategory();
                    $response = $action->handle($category->id);
                    $this->result = $response->getData();
                    
                    // Kategorie existiert, aber keine Produkte
                    if (is_array($this->result) && empty($this->result)) {
                        $this->error = 'Keine Produkte in der Kategorie "' . $category->name . '" (ID: ' . $category->id . ') gefunden.';
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
                        $this->validate([
                            'productId' => 'integer',
                            'productName' => 'string'
                        ]);
                        
                        $action = new GetProduct();
                        $response = $action->handleByIdAndName($this->productId, $this->productName);
                        
                        if ($response->status() === 404) {
                            $this->error = 'Produkt-ID "' . $this->productId . '" und Produktname "' . $this->productName . '" gehören nicht zum selben Produkt oder das Produkt existiert nicht.';
                            break;
                        }
                        
                        $this->result = $response->getData();
                        break;
                    }
                    
                    // Nur ein Feld ausgefüllt: Normale Suche
                    $action = new GetProduct();
                    $response = null;
                    $searchTerm = '';
                    $searchType = '';
                    
                    if (!empty($this->productId)) {
                        if (!preg_match('/^\d+$/', trim($this->productId))) {
                            $this->error = 'Produkt-ID muss eine gültige Ganzzahl sein.';
                            break;
                        }
                        $this->validate(['productId' => 'integer']);
                        $response = $action->handle($this->productId);
                        $searchTerm = $this->productId;
                        $searchType = 'ID';
                    } else {
                        $this->validate(['productName' => 'string']);
                        $response = $action->handleByName($this->productName);
                        $searchTerm = $this->productName;
                        $searchType = 'Name';
                    }
                    
                    // Produkt existiert nicht
                    if ($response->status() === 404) {
                        $this->error = 'Produkt mit ' . $searchType . ' "' . $searchTerm . '" existiert nicht oder der Produktname ist unvollständig.';
                        break;
                    }
                    
                    $this->result = $response->getData();
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
                    
                    // Prüfe ob Kategorie existiert
                    $categoryAction = new GetCategory();
                    $categoryResponse = $categoryAction->handle($this->categoryIdForCreate);
                    if ($categoryResponse->status() === 404) {
                        $this->error = 'Kategorie mit ID ' . $this->categoryIdForCreate . ' existiert nicht.';
                        break;
                    }
                    
                    $this->validate([
                        'name' => 'required|string|max:255',
                        'price' => 'required|numeric|min:0',
                        'categoryIdForCreate' => 'required|integer',
                        'stockQuantity' => 'required|integer|min:0',
                    ]);
                    $action = new CreateProduct();
                    $this->result = $action->handle([
                        'name' => $this->name,
                        'price' => $this->price,
                        'category_id' => $this->categoryIdForCreate,
                        'stock_quantity' => $this->stockQuantity,
                    ]);
                    break;
                    
                case 'create-category':
                    // Nur Admins und Manager dürfen Kategorien erstellen
                    if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
                        $this->error = 'Nur Administratoren und Manager dürfen Kategorien erstellen.';
                        break;
                    }
                    
                    if (empty($this->categoryNameForCreate)) {
                        $this->error = 'Bitte geben Sie einen Kategorienamen ein.';
                        break;
                    }
                    
                    $this->validate([
                        'categoryNameForCreate' => 'required|string|max:255',
                    ]);
                    
                    $action = new CreateCategory();
                    $this->result = $action->handle([
                        'name' => $this->categoryNameForCreate,
                    ]);
                    break;
                
                case 'delete-category':
                    // Nur Admins und Manager dürfen Kategorien löschen
                    if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
                        $this->error = 'Nur Administratoren und Manager dürfen Kategorien löschen.';
                        break;
                    }
                    
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
                    
                    // Prüfe ob Kategorie existiert
                    $category = Category::find($this->categoryId);
                    if (!$category) {
                        $this->error = 'Kategorie mit ID "' . $this->categoryId . '" existiert nicht.';
                        break;
                    }
                    
                    // Prüfe ob Name übereinstimmt
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
                    
                    // Prüfe ob Kategorie existiert
                    $categoryAction = new GetCategory();
                    $categoryResponse = $categoryAction->handle($this->categoryIdForCreate);
                    if ($categoryResponse->status() === 404) {
                        $this->error = 'Kategorie mit ID ' . $this->categoryIdForCreate . ' existiert nicht.';
                        break;
                    }
                    
                    $this->validate([
                        'productId' => 'required|integer',
                        'name' => 'required|string|max:255',
                        'price' => 'required|numeric|min:0',
                        'categoryIdForCreate' => 'required|integer',
                    ]);
                    $action = new GetProduct();
                    $response = $action->handle($this->productId);
                    
                    if ($response->status() === 404) {
                        $this->error = 'Produkt mit ID ' . $this->productId . ' nicht gefunden.';
                        break;
                    }
                    
                    $product = Product::findOrFail($this->productId);
                    $updateAction = new UpdateProduct();
                    $this->result = $updateAction->handle($product, [
                        'name' => $this->name,
                        'price' => $this->price,
                        'category_id' => $this->categoryIdForCreate,
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
                    
                    $this->validate([
                        'productId' => 'required|integer',
                        'productName' => 'required|string'
                    ]);
                    
                    // Prüfen ob ID und Name zum selben Produkt gehören
                    $action = new GetProduct();
                    $response = $action->handleByIdAndName($this->productId, $this->productName);
                    
                    if ($response->status() === 404) {
                        $this->error = 'Produkt-ID "' . $this->productId . '" und Produktname "' . $this->productName . '" gehören nicht zum selben Produkt oder das Produkt existiert nicht.';
                        break;
                    }
                    
                    $productData = $response->getData();
                    $product = Product::findOrFail($this->productId);
                    $deleteAction = new DeleteProduct();
                    $deleteAction->handle($product);
                    
                    // Speichere Produktdaten für Anzeige (Produkt ist bereits gelöscht)
                    $this->result = [$productData]; // Als Array für Tabellen-Anzeige
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
                    
                    $this->validate([
                        'productId' => 'required|integer',
                        'productName' => 'required|string',
                    ]);
                    
                    $prodInfo = [
                        'product_id' => $this->productId,
                        'product_name' => $this->productName,
                        'adjustment' => (int)$this->adjustment,
                    ];
                    $action = new StockAdjustment();
                    
                    // Prüfe ob Produkt existiert
                    $prod = $action->handleIfProdExists($prodInfo);
                    if (!$prod) {
                        $this->error = 'Produkt mit ID ' . $this->productId . ' nicht gefunden.';
                        break;
                    }
                    
                    // Prüfe ob Produktname übereinstimmt
                    $prod = $action->handleIfProdNamComplies($prod, $prodInfo);
                    if (!$prod) {
                        $this->error = 'Eingegebener Produktname "' . $this->productName . '" stimmt nicht mit dem gespeicherten Produktnamen überein.';
                        break;
                    }
                    
                    // Prüfe ob Bestand 100 nicht überschreitet
                    if (!$action->handleIfStockFull($prod, $prodInfo)) {
                        $neuerBestand = $prod->count + (int)$this->adjustment;
                        $this->error = 'Bestandsanpassung würde die Obergrenze von 100 überschreiten. Aktueller Bestand: ' . $prod->count . ', Anpassung: ' . $this->adjustment . ', Neuer Bestand wäre: ' . $neuerBestand;
                        break;
                    }
                    
                    // Prüfe ob Bestand nicht negativ wird
                    if (!$action->handleIfStockNegative($prod, $prodInfo)) {
                        $this->error = 'Bestandsanpassung würde zu einem negativen Bestand führen. Aktueller Bestand: ' . $prod->count . ', Anpassung: ' . $this->adjustment;
                        break;
                    }
                    
                    $this->result = $prod->fresh();
                    break;
                
                case 'view-categories':
                    $categories = Category::with([
                        'products',
                        'productsWithLowStock',
                        'productsOutOfStock'
                    ])->withCount([
                        'products',
                        'productsWithLowStock',
                        'productsOutOfStock'
                    ])->orderBy('name')->get();
                    
                    $this->result = $categories;
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

    public function render()
    {
        return view('livewire.dashboard');
    }
}
